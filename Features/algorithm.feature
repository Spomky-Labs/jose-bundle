Feature: Algorithms Manager
  In order get encrypt, decrypt, sign and verify signatures,
  the algorithm manager should be available and have algorithms enabled

  Scenario: A resource owner accepted the client
    When I list algorithms
    Then I should get a list of algorithms

  Scenario: Show JWKSet page
    When I am on "https://local.dev/keys"
    Then print last response

  Scenario: Show JWK page
    When I am on "https://local.dev/key/ABCD"
    Then print last response
