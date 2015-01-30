Feature: A client request an authorization
  In order get a protected resource
  A client must get an authorization from resource owner

  Scenario: A resource owner accepted the client
    When I am on the page "https://oauth2.test/api/secured/foo"
    Then I should receive an authentication error
    And then required scope is "scope1 scope2"
