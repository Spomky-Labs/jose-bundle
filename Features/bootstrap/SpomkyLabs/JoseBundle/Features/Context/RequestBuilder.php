<?php

namespace SpomkyLabs\JoseBundle\Features\Context;

use Symfony\Component\HttpFoundation\Request;

class RequestBuilder
{
    private $query = array();
    private $fragment = array();
    private $server = array();
    private $header = array();
    private $content_parameter = array();
    private $content = null;
    private $method = 'GET';
    private $uri = '/';

    public function getUri()
    {
        $parse_url = parse_url($this->uri);
        $parse_url["query"] = array_merge(isset($parse_url["query"]) ? $parse_url["query"] : array(), $this->query);
        $parse_url["fragment"] = array_merge(isset($parse_url["fragment"]) ? $parse_url["fragment"] : array(), $this->fragment);
        if (count($parse_url["query"]) === 0) {
            unset($parse_url["query"]);
        }
        if (count($parse_url["fragment"]) === 0) {
            unset($parse_url["fragment"]);
        }

        return
            ((isset($parse_url["scheme"])) ? $parse_url["scheme"]."://" : "")
            .((isset($parse_url["user"])) ? $parse_url["user"].((isset($parse_url["pass"])) ? ":".$parse_url["pass"] : "")."@" : "")
            .((isset($parse_url["host"])) ? $parse_url["host"] : "")
            .((isset($parse_url["port"])) ? ":".$parse_url["port"] : "")
            .((isset($parse_url["path"])) ? $parse_url["path"] : "")
            .((isset($parse_url["query"])) ? "?".http_build_query($parse_url["query"]) : "")
            .((isset($parse_url["fragment"])) ? "#".http_build_query($parse_url["fragment"]) : "");
    }

    public function addFragmentParameter($key, $value)
    {
        $this->fragment[$key] = $value;

        return $this;
    }

    public function removeFragmentParameter($key)
    {
        unset($this->fragment[$key]);

        return $this;
    }

    public function addQueryParameter($key, $value)
    {
        $this->query[$key] = $value;

        return $this;
    }

    public function removeQueryParameter($key)
    {
        unset($this->query[$key]);

        return $this;
    }

    /**
     * @param string $key
     */
    public function addServer($key, $value)
    {
        $this->server[$key] = $value;

        return $this;
    }

    public function removeServer($key)
    {
        unset($this->server[$key]);

        return $this;
    }

    public function addHeader($key, $value)
    {
        $this->header[$key] = $value;

        return $this;
    }

    public function removeHeader($key)
    {
        unset($this->header[$key]);

        return $this;
    }

    public function addContentParameter($key, $value)
    {
        $this->content_parameter[$key] = $value;

        return $this;
    }

    public function removeContentParameter($key)
    {
        unset($this->content_parameter[$key]);

        return $this;
    }

    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    public function unsetContent()
    {
        $this->content = null;

        return $this;
    }

    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    public function unsetMethod()
    {
        $this->method = 'GET';

        return $this;
    }

    /**
     * @param string $uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    public function unsetUri()
    {
        $this->uri = '/';

        return $this;
    }

    public function getServer()
    {
        $data = $this->server;
        foreach ($this->header as $key => $value) {
            $data[strtoupper('HTTP_'.$key)] = $value;
        }

        return $data;
    }

    public function getContent()
    {
        if ($this->content !== null) {
            return $this->content;
        }
        if (count($this->content_parameter) > 0) {
            return http_build_query($this->content_parameter);
        }
    }

    public function getRequest()
    {
        return Request::create(
            $this->getUri(),
            $this->method,
            array(),
            array(),
            array(),
            $this->getServer(),
            $this->getContent()
        );
    }
}
