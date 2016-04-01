Feature: This bundle is able to use keys and key sets
  These keys are available through services
  Key sets include keys and are available through services too

  Scenario: A Key is available through a service
    When I try to get the key "jose.key.key1" and store it in the variable "signing_key"
    Then the variable "signing_key" should be an object that implements "\Jose\Object\JWKInterface"

  Scenario: A Key is available through a service
    When I try to get the key "jose.key.key6" and store it in the variable "google_certificate_chain"
    Then the variable "google_certificate_chain" should be an object that implements "\Jose\Object\JWKInterface"

  Scenario: A Key Set is available through a service
    When I try to get the key "jose.key_set.jwkset1" and store it in the variable "from_serialized_jwkset"
    Then the variable "from_serialized_jwkset" should be an object that implements "\Jose\Object\JWKSetInterface"

  Scenario: A Key Set is available through a service and loaded from an URL
    When I try to get the key "jose.key_set.jwkset2" and store it in the variable "from_key_ids"
    Then the variable "from_key_ids" should be an object that implements "\Jose\Object\JWKSetInterface"

  Scenario: A Key Set is available through a service and loaded from an URL
    When I try to get the key "jose.key_set.jwkset3" and store it in the variable "google_key_set_in_jwkset_format"
    Then the variable "google_key_set_in_jwkset_format" should be an object that implements "\Jose\Object\JWKSetInterface"

  Scenario: A Key Set is available through a service and loaded from an URL
    When I try to get the key "jose.key_set.jwkset4" and store it in the variable "google_key_set_in_x509_certificates"
    Then the variable "google_key_set_in_x509_certificates" should be an object that implements "\Jose\Object\JWKSetInterface"
