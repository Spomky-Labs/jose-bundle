Feature: Sign data
  In order to sign data the library must be able to use an algorithm and a key.

  Scenario: I want to sign a message
    Given I want to use the following key to sign the message
    """
    kty:oct
    kid:018c0ae5-4d9b-471b-bfd6-eef314bc7037
    use:sig
    alg:HS256
    k:hJtXIZ2uSN5kbQfbtTNWbpdmhkV8FJG-Onbc6mxCcYg
    """
    And I add value "HS256" at key "alg" in the protected header
    And I add value "018c0ae5-4d9b-471b-bfd6-eef314bc7037" at key "kid" in the protected header
    And the payload is "It’s a dangerous business, Frodo, going out your door. You step onto the road, and if you don't keep your feet, there’s no knowing where you might be swept off to."
    When I try to sign the input
    Then the signed message is "eyJhbGciOiJIUzI1NiIsImtpZCI6IjAxOGMwYWU1LTRkOWItNDcxYi1iZmQ2LWVlZjMxNGJjNzAzNyJ9.SXTigJlzIGEgZGFuZ2Vyb3VzIGJ1c2luZXNzLCBGcm9kbywgZ29pbmcgb3V0IHlvdXIgZG9vci4gWW91IHN0ZXAgb250byB0aGUgcm9hZCwgYW5kIGlmIHlvdSBkb24ndCBrZWVwIHlvdXIgZmVldCwgdGhlcmXigJlzIG5vIGtub3dpbmcgd2hlcmUgeW91IG1pZ2h0IGJlIHN3ZXB0IG9mZiB0by4.s0h6KThzkfBBBkLspW1h84VsJZFTsPPqMDA7g1Md7p0"


  Scenario: I want to sign claims
    Given I want to use the following key to sign the message
    """
    kty:oct
    kid:018c0ae5-4d9b-471b-bfd6-eef314bc7037
    use:sig
    alg:HS256
    k:hJtXIZ2uSN5kbQfbtTNWbpdmhkV8FJG-Onbc6mxCcYg
    """
    And I add value "HS256" at key "alg" in the protected header
    And I add value "018c0ae5-4d9b-471b-bfd6-eef314bc7037" at key "kid" in the protected header
    And I add the claim "foo" with value "bar"
    And the signature is detached
    When I try to sign the input
    Then the result is a signed JWT
    Then the detached payload is not null
