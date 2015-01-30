<?php

namespace SpomkyLabs\JoseBundle\Features\Context;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Behat context class.
 */
class FeatureContext extends MinkContext implements SnippetAcceptingContext
{
    use KernelDictionary;

    private $request_builder;
    private $exception;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context object.
     * You can also pass arbitrary arguments to the context constructor through behat.yml.
     */
    public function __construct()
    {
        $this->exception = null;
        $this->request_builder = new RequestBuilder();
        AnnotationRegistry::registerFile('./Annotation/OAuth2.php');
        AnnotationRegistry::registerAutoloadNamespaces(array(
            'Sensio\\Bundle\\FrameworkExtraBundle' => './vendor/sensio/framework-extra-bundle/',
        ));
    }

    public function getRequestBuilder()
    {
        return $this->request_builder;
    }

    public function getException()
    {
        return $this->exception;
    }

    /**
     * @Given I add key :key with value :value in the header
     */
    public function iAddKeyWithValueInTheHeader($key, $value)
    {
        $this->getRequestBuilder()->addHeader($key, $value);
    }

    /**
     * @Given I add key :key with value :value in the query parameter
     */
    public function iAddKeyWithValueInTheQueryParameter($key, $value)
    {
        $this->getRequestBuilder()->addQueryParameter($key, $value);
    }

    /**
     * @Given I add key :key with value :value in the body request
     */
    public function iAddKeyWithValueInTheBodyRequest($key, $value)
    {
        $this->getRequestBuilder()->addContentParameter($key, $value);
    }

    /**
     * @Given the content type is :content_type
     */
    public function theContentTypeIs($content_type)
    {
        $this->getRequestBuilder()->addServer('CONTENT_TYPE', $content_type);
    }

    /**
     * @Given the request is not secured
     */
    public function theRequestIsNotSecured()
    {
        $this->getRequestBuilder()->addServer('HTTPS', 'off');
    }

    /**
     * @Given the request is secured
     */
    public function theRequestIsSecured()
    {
        $this->getRequestBuilder()->addServer('HTTPS', 'on');
    }

    /**
     * @Given I am logged in as :username
     */
    public function iAmAnLoggedInAs($username)
    {
        $client = $this->getSession()->getDriver()->getClient();
        $client->getCookieJar()->set(new Cookie(session_name(), true));

        $session = $client->getContainer()->get('session');

        $user = $this->kernel->getContainer()->get('spomky_jose_test.end_user_manager')->getEndUser($username);

        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $session->set('_security_main', serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);
    }

    /**
     * @When I :method the request to :uri
     * @param string $method
     */
    public function iTheRequestTo($method, $uri)
    {
        $client = $this->getSession()->getDriver()->getClient();
        $client->followRedirects(false);

        $this->getRequestBuilder()->setUri($this->locatePath($uri));
        try {
            $client->request(
                $method,
                $this->getRequestBuilder()->getUri(),
                array(),
                array(),
                $this->getRequestBuilder()->getServer(),
                $this->getRequestBuilder()->getContent()
            );
        } catch (\Exception $e) {
            $this->exception = $e;
        }
        $client->followRedirects(true);
    }

    /**
     * @Then I should receive a :content_type response
     */
    public function iShouldReceiveAResponse($content_type)
    {
        $headers = $this->getSession()->getResponseHeaders();
        if (!isset($headers['content-type']) || !in_array($content_type, $headers['content-type'])) {
            throw new \Exception('The response header does not contain "'.$content_type.'"');
        }
    }

    /**
     * @Then the response is a JSON object
     */
    public function theResponseIsAJsonObject()
    {
        $content = $this->getSession()->getPage()->getContent();
        $data = json_decode($content, true);

        if (!$data) {
            throw new \Exception('The content is not a JSON object');
        }
    }

    /**
     * @Then the body contains an access token
     */
    public function theBodyContainsAnAccessToken()
    {
        $content = $this->getSession()->getPage()->getContent();
        $data = json_decode($content, true);

        if (!isset($data['access_token'])) {
            throw new \Exception('The content does not contain an access token');
        }
    }

    /**
     * @Then the status code of the response is :code
     */
    public function theStatusCodeOfTheResponseIs($code)
    {
        if ($this->getSession()->getStatusCode() !== (int) $code) {
            throw new \Exception('The status code of the response is not "'.$code.'"');
        }
    }

    /**
     * @Then I should receive an OAuth2 exception with message :message and description :scheme
     */
    public function iShouldReceiveAnOauth2ExceptionWithMessageAndDescription($message, $description)
    {
        if ($this->exception instanceof \Exception) {
            throw $this->exception;
        }

        $content = $this->getSession()->getPage()->getContent();
        $data = json_decode($content, true);

        if (!$data) {
            throw new \Exception('The response is not an OAuth2 Exception.');
        }

        if (!isset($data['error_description']) || !isset($data['error'])) {
            throw new \Exception('The response is not an OAuth2 Exception.');
        }

        if ($data['error'] !== $message) {
            throw new \Exception('The error should be "'.$message.'" but I get "'.$data['error'].'"');
        }

        if ($data['error_description'] !== $description) {
            throw new \Exception('The error should be "'.$description.'" but I get "'.$data['error_description'].'"');
        }
    }

    /**
     * @Then I should not receive an exception
     */
    public function iShouldNotReceiveAnException()
    {
        if ($this->getException() instanceof \Exception) {
            throw $this->getException();
        }
    }

    /**
     * @Then I should receive an exception :message
     */
    public function iShouldReceiveAnException($message)
    {
        if (!$this->getException() instanceof \Exception) {
            throw new \Exception('No exception catched');
        }

        if ($message !== $this->getException()->getMessage()) {
            throw new \Exception('The exception has not the expected message: "'.$message.'"');
        }
    }

    /**
     * @Then I should receive an error :message
     */
    public function iShouldReceiveAnError($message)
    {
        $content = $this->getSession()->getPage()->getContent();
        $data = json_decode($content, true);

        if (!isset($data["error"])) {
            throw new \Exception('The response does not contain an error');
        }

        if ($data["error"] !== $message) {
            throw new \Exception('The response contains an error, but its value is "'.$data['error'].'"');
        }
    }

    /**
     * @Then I should be redirected using the :scheme scheme
     */
    public function iShouldBeRedirectedUsingTheScheme($scheme)
    {
        if ($this->getSession()->getStatusCode() !== 301 && $this->getSession()->getStatusCode() !== 302) {
            throw new \Exception('The status code is not a redirection');
        }

        $header = $this->getSession()->getResponseHeaders();

        if (!isset($header['location'])) {
            throw new \Exception('The header does not contain a redirection URL');
        }

        $uri = parse_url($header['location']);

        if ($scheme !== $uri['scheme']) {
            throw new \Exception('The redirection Uri does not use "'.$scheme.'" scheme');
        }
    }

    /**
     * @Given I am on the page :url
     */
    public function iAmOnThePage($url)
    {
        $this->iTheRequestTo("GET", $url);
    }

    /**
     * @When I click on :arg1
     */
    public function iClickOn($name)
    {
        $this->getSession()->getDriver()->getClient()->followRedirects(false);

        $button = $this->fixStepArgument($name);
        $this->getSession()->getPage()->pressButton($button);

        $this->getSession()->getDriver()->getClient()->followRedirects(true);
    }

    /**
     * @Then I should be redirected
     */
    public function iShouldBeRedirected()
    {
        $headers = $this->getSession()->getResponseHeaders();
        if (!isset($headers['location'])) {
            throw new \Exception('There is no redirection in the response');
        }
    }

    /**
     * @Then the redirect query should contain parameter :param
     */
    public function theRedirectQueryShouldContainParameter($param)
    {
        $headers = $this->getSession()->getResponseHeaders();
        $uri = parse_url(current($headers['location']));

        if (!isset($uri['query'])) {
            throw new \Exception('The query does not contain any parameter');
        }
        parse_str($uri['query'], $uri['query']);
        if (!isset($uri['query'][$param])) {
            throw new \Exception('The query does not contain parameter "'.$param.'"');
        }
    }

    /**
     * @Then the redirect query should contain parameter :param with value :value
     */
    public function theRedirectQueryShouldContainParameterWithValue($param, $value)
    {
        $headers = $this->getSession()->getResponseHeaders();
        $uri = parse_url(current($headers['location']));
        parse_str($uri['query'], $uri['query']);
        if (!isset($uri['query'][$param])) {
            throw new \Exception('The query does not contain parameter "'.$param.'"');
        }

        if ($uri['query'][$param] !== $value) {
            throw new \Exception('The value is not "'.$value.'", I got "'.$uri['query'][$param].'"');
        }
    }

    /**
     * @Then the redirection starts with :location
     */
    public function theRedirectionStartsWith($location)
    {
        $headers = $this->getSession()->getResponseHeaders();
        foreach ($headers['location'] as $url) {
            if (substr($url, 0, strlen($location)) === $location) {
                return;
            }
        }
        throw new \Exception('The redirection URL does not contain "'.$location.'". The complete values are "'.json_encode($headers['location']).'"');
    }

    /**
     * @Then I should receive an access token
     */
    public function iShouldReceiveAnAccessToken()
    {
        $headers = $this->getSession()->getResponseHeaders();
        $content = $this->getSession()->getPage()->getContent();
        $data = json_decode($content, true);

        if (!isset($headers['content-type']) && $headers['content-type'] !== 'application/json') {
            throw new \Exception('The content is not a JSON object');
        }

        if (!is_array($data)) {
            throw new \Exception('The content is not an array');
        }

        if (!isset($data['access_token'])) {
            throw new \Exception('The content is not an access token');
        }
    }

    /**
     * @Then the error should contain parameter :param
     */
    public function theErrorShouldContainParameter($param)
    {
        $content = $this->getSession()->getPage()->getContent();
        $data = json_decode($content, true);

        if (!isset($data[$param])) {
            throw new \Exception('The access token does not contain parameter "'.$param.'"');
        }
    }

    /**
     * @Then the error should contain parameter :param with value :value
     */
    public function theErrorShouldContainParameterWithValue($param, $value)
    {
        $content = $this->getSession()->getPage()->getContent();
        $data = json_decode($content, true);

        if (!isset($data[$param])) {
            throw new \Exception('The access token does not contain parameter "'.$param.'"');
        }

        if ($data[$param] !== $value) {
            throw new \Exception('The access token contains parameter "'.$param.'", but its value is "'.$data[$param].'"');
        }
    }

    /**
     * @Then the error has :param with value :value
     */
    public function theErrorHasWithValue($param, $value)
    {
        $content = $this->getSession()->getPage()->getContent();
        $data = json_decode($content, true);

        if (!isset($data[$param])) {
            throw new \Exception('The access token does not contain parameter "'.$param.'"');
        }

        if ($data[$param] !== $value) {
            throw new \Exception('The access token contains parameter "'.$param.'", but its value is "'.$data[$param].'"');
        }
    }

    /**
     * @Then the access token should contain parameter :param with value :value
     */
    public function theAccessTokenShouldContainParameterWithValue($param, $value)
    {
        $content = $this->getSession()->getPage()->getContent();
        $data = json_decode($content, true);

        if (!isset($data[$param])) {
            throw new \Exception('The access token does not contain parameter "'.$param.'"');
        }

        if ($data[$param] !== $value) {
            throw new \Exception('The access token contains parameter "'.$param.'", but its value is "'.$data[$param].'"');
        }
    }

    /**
     * @Then I should receive an authentication error
     */
    public function iShouldReceiveAnAuthenticationError()
    {
        $headers = $this->getSession()->getResponseHeaders();
        if (!array_key_exists('www-authenticate', $headers)) {
            throw new \Exception('There is no authentication error');
        }
    }

    /**
     * @Then then required scope is :scope
     */
    public function thenRequiredScopeIs($scope)
    {
        $headers = $this->getSession()->getResponseHeaders();
        $authentication = substr(current($headers['www-authenticate']),7);
        preg_match_all('@(scope)=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $authentication, $matches, PREG_SET_ORDER);
        if (1 !== count($matches)) {
            throw new \Exception('There is no scope restriction');
        }
        if (1 !== count($matches) || $scope !== $matches[0][3]) {
            throw new \Exception('The scope restriction is "'.$matches[0][3].'" ("'.$scope.'" expected)');
        }
    }
}
