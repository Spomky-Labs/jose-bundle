Feature: This bundle is able to use keys and key sets
  These keys are available through services
  Key sets include keys and are available through services too

  Scenario: A Key is available through a service
    When I try to get the key "jose.key.key1" and store it in the variable "signing_key"
    Then the variable "signing_key" should be an object that implements "\Jose\Object\JWKInterface"
  Scenario: A Key Set is available through a service
    When I try to get the key "jose.key_set.jwkset1" and store it in the variable "signing_key_set"
    Then the variable "signing_key_set" should be an object that implements "\Jose\Object\JWKSetInterface"

  Scenario: A Key Set is available through a service and loaded from an URL
    When I try to get the key "jose.key_set.jwkset3" and store it in the variable "google_key_set"
    Then the variable "google_key_set" should be an object that implements "\Jose\Object\JWKSetInterface"

