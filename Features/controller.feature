Feature: JWKSet Controller
  In order to share public keys to clients
  who need to verify signatures or send encrypted data,
  the key set should be available

  Scenario: I can get JWKSet
    When I am on "https://local.dev/keys"
    Then The content type is "application/json"
    And I should see a valid key set
    And the key set contains at least one key
