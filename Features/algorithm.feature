Feature: Algorithms Manager
  In order get encrypt, decrypt, sign and verify signatures,
  the algorithm manager should be available and have algorithms enabled

  Scenario: A resource owner accepted the client
    When I list algorithms
    Then I should get a list of algorithms
