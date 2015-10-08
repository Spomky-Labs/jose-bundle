Feature: Load JWT
  In order to read signed or encrypted content
  the library must be able to load data and verify signatures
  or decrypt data. If data is compress, the library can decompress it.

  Scenario: I can load a JWS and verify the signature (ES512 algorithm)
    Given I have the following key in my key set
    """
    kty:EC
    kid:bilbo.baggins@hobbiton.example
    use:sig
    crv:P-521
    x:AHKZLLOsCOzz5cY97ewNUajB957y-C-U88c3v13nmGZx6sYl_oJXu9A5RkTKqjqvjyekWF-7ytDyRXYgCF5cj0Kt
    y:AdymlHvOiLxXkEhayXQnNCvDX4h9htZaCJN34kfmC6pV5OhQHiraVySsUdaQkAgDPrwQrJmbnX9cwlGfP-HqHZR1
    """
    When I try to load the following data
    """
    eyJhbGciOiJFUzUxMiIsImtpZCI6ImJpbGJvLmJhZ2dpbnNAaG9iYml0b24uZXhhbXBsZSJ9.SXTigJlzIGEgZGFuZ2Vyb3VzIGJ1c2luZXNzLCBGcm9kbywgZ29pbmcgb3V0IHlvdXIgZG9vci4gWW91IHN0ZXAgb250byB0aGUgcm9hZCwgYW5kIGlmIHlvdSBkb24ndCBrZWVwIHlvdXIgZmVldCwgdGhlcmXigJlzIG5vIGtub3dpbmcgd2hlcmUgeW91IG1pZ2h0IGJlIHN3ZXB0IG9mZiB0by4.AE_R_YZCChjn4791jSQCrdPZCNYqHXCTZH0-JZGYNlaAjP2kqaluUIIUnC9qvbu9Plon7KRTzoNEuT4Va2cmL1eJAQy3mtPBu_u_sDDyYjnAMDxXPn7XrT0lw-kvAD890jl8e2puQens_IEKBpHABlsbEPX6sFY8OcGDqoRuBomu9xQ2
    """
    Then the loaded data is a JWS
    And the payload of the loaded data is "It’s a dangerous business, Frodo, going out your door. You step onto the road, and if you don't keep your feet, there’s no knowing where you might be swept off to."
    And the JWT payload "jti" is null
    And the algorithm of the loaded data is "ES512"

  Scenario: I can load a JWS and verify the signature (HS256 algorithm)
    Given I have the following key in my key set
    """
    kty:oct
    kid:018c0ae5-4d9b-471b-bfd6-eef314bc7037
    use:sig
    alg:HS256
    k:hJtXIZ2uSN5kbQfbtTNWbpdmhkV8FJG-Onbc6mxCcYg
    """
    When I try to load the following data
    """
    eyJhbGciOiJIUzI1NiIsImtpZCI6IjAxOGMwYWU1LTRkOWItNDcxYi1iZmQ2LWVlZjMxNGJjNzAzNyJ9.SXTigJlzIGEgZGFuZ2Vyb3VzIGJ1c2luZXNzLCBGcm9kbywgZ29pbmcgb3V0IHlvdXIgZG9vci4gWW91IHN0ZXAgb250byB0aGUgcm9hZCwgYW5kIGlmIHlvdSBkb24ndCBrZWVwIHlvdXIgZmVldCwgdGhlcmXigJlzIG5vIGtub3dpbmcgd2hlcmUgeW91IG1pZ2h0IGJlIHN3ZXB0IG9mZiB0by4.s0h6KThzkfBBBkLspW1h84VsJZFTsPPqMDA7g1Md7p0
    """
    Then the loaded data is a JWS
    And the payload of the loaded data is "It’s a dangerous business, Frodo, going out your door. You step onto the road, and if you don't keep your feet, there’s no knowing where you might be swept off to."
    And the JWT payload "jti" is null
    And the algorithm of the loaded data is "HS256"

  Scenario: I can load a JWS, but the signature is not valid
    Given I have the following key in my key set
    """
    kty:oct
    kid:018c0ae5-4d9b-471b-bfd6-eef314bc7037
    use:sig
    alg:HS256
    k:hJtXIZ2uSN5kbQfbtTNWbpdmhkV8FJG-Onbc6mxCcYg
    """
    When I try to load the following data
    """
    eyJhbGciOiJIUzI1NiIsImtpZCI6IjAxOGMwYWU1LTRkOWItNDcxYi1iZmQ2LWVlZjMxNGJjNzAzNyJ9.SXTigJlzIGEgZGFuZ2Vyb3VzIGJ1c2luZXNzLCBGcm9kbywgZ29pbmcgb3V0IHlvdXIgZG9vci4gWW91IHN0ZXAgb250byB0aGUgcm9hZCwgYW5kIGlmIHlvdSBkb24ndCBrZWVwIHlvdXIgZmVldCwgdGhlcmXigJlzIG5vIGtub3dpbmcgd2hlcmUgeW91IG1pZ2h0IGJlIHN3ZXB0IG9mZiB0by4.AE_R_YZCChjn4791jSQCrdPZCNYqHXCTZH0-JZGYNlaAjP2kqaluUIIUnC9qvbu9Plon7KRTzoNEuT4Va2cmL1eJAQy3mtPBu_u_sDDyYjnAMDxXPn7XrT0lw-kvAD890jl8e2puQens_IEKBpHABlsbEPX6sFY8OcGDqoRuBomu9xQ2
    """
    Then I should receive an exception
    And the exception message is "Unable to load the input"
