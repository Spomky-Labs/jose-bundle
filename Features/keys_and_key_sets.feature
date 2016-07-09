Feature: This bundle is able to use keys and key sets
  These keys are available through services
  Key sets include keys and are available through services too

  Scenario: A Key is available through a service
    When the service "jose.key.key1" should be an object that implements "\Jose\Object\JWKInterface"

  Scenario: A Key is available through a service
    When the service "jose.key.key6" should be an object that implements "\Jose\Object\JWKInterface"

  Scenario: A Key is available through a service
    When the service "jose.key.key11" should be an object that implements "\Jose\Object\JWKInterface"

  Scenario: A Key Set is available through a service
    When the service "jose.key_set.jwkset1" should be an object that implements "\Jose\Object\JWKSetInterface"
    And the keyset in the service "jose.key_set.jwkset1" contains 2 keys

  Scenario: A Key Set is available through a service and loaded from an URL
    When the service "jose.key_set.jwkset2" should be an object that implements "\Jose\Object\JWKSetInterface"
    And the keyset in the service "jose.key_set.jwkset2" contains 4 keys

  Scenario: A Key Set is available through a service and loaded from an URL
    When the service "jose.key_set.jwkset3" should be an object that implements "\Jose\Object\JWKSetInterface"
    And the keyset in the service "jose.key_set.jwkset3" contains keys

  Scenario: A Key Set is available through a service and loaded from an URL
    When the service "jose.key_set.jwkset4" should be an object that implements "\Jose\Object\JWKSetInterface"
    And the keyset in the service "jose.key_set.jwkset4" contains keys
