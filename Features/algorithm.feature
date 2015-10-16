Feature: Algorithms Manager
  In order to get encrypt/decrypt data or sign/verify signatures,
  the algorithm manager should support algorithms

  Scenario: I want to verify that the algorithm manager supports at least on algorithm
    When I list algorithms
    Then I should get a non empty list of algorithms
