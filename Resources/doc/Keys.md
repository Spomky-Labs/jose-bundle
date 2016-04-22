Keys and Key Sets
=================

# Load Your Keys

This bundle allows you to load keys from various sources: a certificate, a public or private key (may be encrypted), values...
Your keys are identified by an ID and, when loaded, are available as a service container so that you can inject or get them easily.

The service create is `jose.key.YOUR_KEY_ID`.

## Key From a Public/Private Key File

The following configuration allows you to load a key from a key file. This key can be a RSA or an EC key. Both PEM and DER formats are supported.
If the pivate key is encrypted, your configuration must indicate the password to decrypt it.

```yml
jose:
    keys:
        YOUR_KEY_ID: # This key ID must be unique for each key
            file: # The key is loaded from a key file
                path: "%kernel.root_dir%/Keys/EC/private.es256.encrypted.key" # The path to your file
                password: "test" # The password of your key (if encrypted)
                additional_values: # Additional information to set
                    kid: "PRIVATE_KEY_#42" # We recommend you to set "kid" (the key ID), "use" ("sig" or "enc") and "alg" (algorithm)
                    use: "enc"
                    alg: "RSA-OAEP-256"
```

## Key From a Certificate File

The following configuration allows you to load a key from a certificate file.

```yml
jose:
    keys:
        YOUR_KEY_ID: # This key ID must be unique for each key
            certificate: # The key is loaded from a certificate file
                path: "%kernel.root_dir%/certificates/RSA/DER/8192b-rsa-example-cert.der"
                additional_values: # Additional information to set
                    kid: "abcd-efgh-ijkl-mnop"
                    use: "sig"
                    alg: "RS512"
```

## Key From a Certificate Chain

The following configuration allows you to load a key from a certificate chain.

```yml
jose:
    keys:
        YOUR_KEY_ID: # This key ID must be unique for each key
            x5c: # The key is loaded from a certificate chain
                value: | # The certificate chain on multilines
                        -----BEGIN CERTIFICATE-----
                        MIID8DCCAtigAwIBAgIDAjqDMA0GCSqGSIb3DQEBCwUAMEIxCzAJBgNVBAYTAlVT
                        MRYwFAYDVQQKEw1HZW9UcnVzdCBJbmMuMRswGQYDVQQDExJHZW9UcnVzdCBHbG9i
                        YWwgQ0EwHhcNMTMwNDA1MTUxNTU2WhcNMTYxMjMxMjM1OTU5WjBJMQswCQYDVQQG
                        EwJVUzETMBEGA1UEChMKR29vZ2xlIEluYzElMCMGA1UEAxMcR29vZ2xlIEludGVy
                        bmV0IEF1dGhvcml0eSBHMjCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEB
                        AJwqBHdc2FCROgajguDYUEi8iT/xGXAaiEZ+4I/F8YnOIe5a/mENtzJEiaB0C1NP
                        VaTOgmKV7utZX8bhBYASxF6UP7xbSDj0U/ck5vuR6RXEz/RTDfRK/J9U3n2+oGtv
                        h8DQUB8oMANA2ghzUWx//zo8pzcGjr1LEQTrfSTe5vn8MXH7lNVg8y5Kr0LSy+rE
                        ahqyzFPdFUuLH8gZYR/Nnag+YyuENWllhMgZxUYi+FOVvuOAShDGKuy6lyARxzmZ
                        EASg8GF6lSWMTlJ14rbtCMoU/M4iarNOz0YDl5cDfsCx3nuvRTPPuj5xt970JSXC
                        DTWJnZ37DhF5iR43xa+OcmkCAwEAAaOB5zCB5DAfBgNVHSMEGDAWgBTAephojYn7
                        qwVkDBF9qn1luMrMTjAdBgNVHQ4EFgQUSt0GFhu89mi1dvWBtrtiGrpagS8wDgYD
                        VR0PAQH/BAQDAgEGMC4GCCsGAQUFBwEBBCIwIDAeBggrBgEFBQcwAYYSaHR0cDov
                        L2cuc3ltY2QuY29tMBIGA1UdEwEB/wQIMAYBAf8CAQAwNQYDVR0fBC4wLDAqoCig
                        JoYkaHR0cDovL2cuc3ltY2IuY29tL2NybHMvZ3RnbG9iYWwuY3JsMBcGA1UdIAQQ
                        MA4wDAYKKwYBBAHWeQIFATANBgkqhkiG9w0BAQsFAAOCAQEAqvqpIM1qZ4PtXtR+
                        3h3Ef+AlBgDFJPupyC1tft6dgmUsgWM0Zj7pUsIItMsv91+ZOmqcUHqFBYx90SpI
                        hNMJbHzCzTWf84LuUt5oX+QAihcglvcpjZpNy6jehsgNb1aHA30DP9z6eX0hGfnI
                        Oi9RdozHQZJxjyXON/hKTAAj78Q1EK7gI4BzfE00LshukNYQHpmEcxpw8u1VDu4X
                        Bupn7jLrLN1nBz/2i8Jw3lsA5rsb0zYaImxssDVCbJAJPZPpZAkiDoUGn8JzIdPm
                        X4DkjYUiOnMDsWCOrmji9D6X52ASCWg23jrW4kOVWzeBkoEfu43XrVJkFleW2V40
                        fsg12A==
                        -----END CERTIFICATE-----
                        -----BEGIN CERTIFICATE-----
                        MIIDVDCCAjygAwIBAgIDAjRWMA0GCSqGSIb3DQEBBQUAMEIxCzAJBgNVBAYTAlVT
                        MRYwFAYDVQQKEw1HZW9UcnVzdCBJbmMuMRswGQYDVQQDExJHZW9UcnVzdCBHbG9i
                        YWwgQ0EwHhcNMDIwNTIxMDQwMDAwWhcNMjIwNTIxMDQwMDAwWjBCMQswCQYDVQQG
                        EwJVUzEWMBQGA1UEChMNR2VvVHJ1c3QgSW5jLjEbMBkGA1UEAxMSR2VvVHJ1c3Qg
                        R2xvYmFsIENBMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA2swYYzD9
                        9BcjGlZ+W988bDjkcbd4kdS8odhM+KhDtgPpTSEHCIjaWC9mOSm9BXiLnTjoBbdq
                        fnGk5sRgprDvgOSJKA+eJdbtg/OtppHHmMlCGDUUna2YRpIuT8rxh0PBFpVXLVDv
                        iS2Aelet8u5fa9IAjbkU+BQVNdnARqN7csiRv8lVK83Qlz6cJmTM386DGXHKTubU
                        1XupGc1V3sjs0l44U+VcT4wt/lAjNvxm5suOpDkZALeVAjmRCw7+OC7RHQWa9k0+
                        bw8HHa8sHo9gOeL6NlMTOdReJivbPagUvTLrGAMoUgRx5aszPeE4uwc2hGKceeoW
                        MPRfwCvocWvk+QIDAQABo1MwUTAPBgNVHRMBAf8EBTADAQH/MB0GA1UdDgQWBBTA
                        ephojYn7qwVkDBF9qn1luMrMTjAfBgNVHSMEGDAWgBTAephojYn7qwVkDBF9qn1l
                        uMrMTjANBgkqhkiG9w0BAQUFAAOCAQEANeMpauUvXVSOKVCUn5kaFOSPeCpilKIn
                        Z57QzxpeR+nBsqTP3UEaBU6bS+5Kb1VSsyShNwrrZHYqLizz/Tt1kL/6cdjHPTfS
                        tQWVYrmm3ok9Nns4d0iXrKYgjy6myQzCsplFAMfOEVEiIuCl6rYVSAlk6l5PdPcF
                        PseKUgzbFbS9bZvlxrFUaKnjaZC2mqUPuLk/IH2uSrW4nOQdtqvmlKXBx4Ot2/Un
                        hw4EbNX/3aBd7YdStysVAq45pmp06drE57xNNB6pXE0zX5IJL4hmXXeXxx12E6nV
                        5fEWCRE11azbJHFwLJhWC9kXtNHjUStedejV0NxPNO3CBWaAocvmMw==
                        -----END CERTIFICATE-----
                additional_values: # Additional information to set
                    kid: "123-456-789"
                    use: "sig"
                    alg: "RS256"
```

## Key From Values

The following configuration allows you to load a key from values.

```yml
jose:
    keys:
        YOUR_KEY_ID: # This key ID must be unique for each key
            values: # The key is loaded from values
                values: # Values of the key (hereafter an RSA private key)
                    kty: "RSA"
                    n: "tpS1ZmfVKVP5KofIhMBP0tSWc4qlh6fm2lrZSkuKxUjEaWjzZSzs72gEIGxraWusMdoRuV54xsWRyf5KeZT0S-I5Prle3Idi3gICiO4NwvMk6JwSBcJWwmSLFEKyUSnB2CtfiGc0_5rQCpcEt_Dn5iM-BNn7fqpoLIbks8rXKUIj8-qMVqkTXsEKeKinE23t1ykMldsNaaOH-hvGti5Jt2DMnH1JjoXdDXfxvSP_0gjUYb0ektudYFXoA6wekmQyJeImvgx4Myz1I4iHtkY_Cp7J4Mn1ejZ6HNmyvoTE_4OuY1uCeYv4UyXFc1s1uUyYtj4z57qsHGsS4dQ3A2MJsw"
                    e: "AQAB"
                    p: "5BGU1c7af_5sFyfsa-onIJgo5BZu8uHvz3Uyb8OA0a-G9UPO1ShLYjX0wUfhZcFB7fwPtgmmYAN6wKGVce9eMAbX4PliPk3r-BcpZuPKkuLk_wFvgWAQ5Hqw2iEuwXLV0_e8c2gaUt_hyMC5-nFc4v0Bmv6NT6Pfry-UrK3BKWc"
                    d: "Kp0KuZwCZGL1BLgsVM-N0edMNitl9wN5Hf2WOYDoIqOZNAEKzdJuenIMhITJjRFUX05GVL138uyp2js_pqDdY9ipA7rAKThwGuDdNphZHech9ih3DGEPXs-YpmHqvIbCd3GoGm38MKwxYkddEpFnjo8rKna1_BpJthrFxjDRhw9DxJBycOdH2yWTyp62ZENPvneK40H2a57W4QScTgfecZqD59m2fGUaWaX5uUmIxaEmtGoJnd9RE4oywKhgN7_TK7wXRlqA4UoRPiH2ACrdU-_cLQL9Jc0u0GqZJK31LDbOeN95QgtSCc72k3Vtzy3CrVpp5TAA67s1Gj9Skn-CAQ"
                    q: "zPD-B-nrngwF-O99BHvb47XGKR7ON8JCI6JxavzIkusMXCB8rMyYW8zLs68L8JLAzWZ34oMq0FPUnysBxc5nTF8Nb4BZxTZ5-9cHfoKrYTI3YWsmVW2FpCJFEjMs4NXZ28PBkS9b4zjfS2KhNdkmCeOYU0tJpNfwmOTI90qeUdU"
                    dp: "aJrzw_kjWK9uDlTeaES2e4muv6bWbopYfrPHVWG7NPGoGdhnBnd70-jhgMEiTZSNU8VXw2u7prAR3kZ-kAp1DdwlqedYOzFsOJcPA0UZhbORyrBy30kbll_7u6CanFm6X4VyJxCpejd7jKNw6cCTFP1sfhWg5NVJ5EUTkPwE66M"
                    dq: "Swz1-m_vmTFN_pu1bK7vF7S5nNVrL4A0OFiEsGliCmuJWzOKdL14DiYxctvnw3H6qT2dKZZfV2tbse5N9-JecdldUjfuqAoLIe7dD7dKi42YOlTC9QXmqvTh1ohnJu8pmRFXEZQGUm_BVhoIb2_WPkjav6YSkguCUHt4HRd2YwE"
                    qi: "BocuCOEOq-oyLDALwzMXU8gOf3IL1Q1_BWwsdoANoh6i179psxgE4JXToWcpXZQQqub8ngwE6uR9fpd3m6N_PL4T55vbDDyjPKmrL2ttC2gOtx9KrpPh-Z7LQRo4BE48nHJJrystKHfFlaH2G7JxHNgMBYVADyttN09qEoav8Os"
```

## Key From a JWK String

The following configuration allows you to load a key from a JWK string.

```yml
jose:
    keys:
        YOUR_KEY_ID: # This key ID must be unique for each key
            jwk: # The key is loaded from a JWK value
                value: '{"kty":"EC","crv":"P-521","d":"Fp6KFKRiHIdR_7PP2VKxz6OkS_phyoQqwzv2I89-8zP7QScrx5r8GFLcN5mCCNJt3rN3SIgI4XoIQbNePlAj6vE","x":"AVpvo7TGpQk5P7ZLo0qkBpaT-fFDv6HQrWElBKMxcrJd_mRNapweATsVv83YON4lTIIRXzgGkmWeqbDr6RQO-1cS","y":"AIs-MoRmLaiPyG2xmPwQCHX2CGX_uCZiT3iOxTAJEZuUbeSA828K4WfAA4ODdGiB87YVShhPOkiQswV3LpbpPGhC","foo":"bar"}'
```

# Load Your Key Sets

Key sets are very useful if you want to group all your keys.


As keys, this bundle allows you to load key sets from various sources: loaded keys, from an URL...
Your key sets are identified by an ID and, when loaded, are available as a service container so that you can inject or get them easily.

The service create is `jose.key_set.YOUR_KEYSET_ID`.

## Key Set From Loaded Keys

If you loaded keys with key IDs `KEY1`, `KEY2` and `KEY3`. Services aliases are `jose.key.KEY1`, `jose.key.KEY2` and `jose.key.KEY3` respectively.

```yml
jose:
    key_sets:
        YOUR_KEYSET_ID: # This key set ID must be unique for each key set
            keys: # The key set is created using loaded keys
                id: # The list of key IDs
                    - jose.key.KEY1
                    - jose.key.KEY2
                    - jose.key.KEY3
```

## Key Set From a JWKSet String

```yml
jose:
    key_sets:
        YOUR_KEYSET_ID: # This key set ID must be unique for each key set
            jwkset: # The key set is loaded from a JWKSet string
                value: '{"keys":[{"kty":"EC","crv":"P-256","x":"f83OJ3D2xF1Bg8vub9tLe1gHMzV76e8Tus9uPHvRVEU","y":"x_FEzRu9m36HLN_tue659LNpXW6pCyStikYjKIWI5a0","use":"sign","key_ops":["sign"],"alg":"ES256","kid":"0123456789"},{"kty":"EC","crv":"P-256","x":"f83OJ3D2xF1Bg8vub9tLe1gHMzV76e8Tus9uPHvRVEU","y":"x_FEzRu9m36HLN_tue659LNpXW6pCyStikYjKIWI5a0","d":"jpsQnnGQmL-YBIffH1136cspYG6-0iY7X1fCE9-E9LI","use":"sign","key_ops":["verify"],"alg":"ES256","kid":"9876543210"}]}'
```

## Key Set From a X509 Url

```yml
jose:
    key_sets:
        YOUR_KEYSET_ID: # This key set ID must be unique for each key set
            x5u: # The key set is loaded from a X509 Url ("x5u" parameter)
                url: "https://www.googleapis.com/oauth2/v1/certs" # Url of the key set
                is_secured: true # If true, only secured connections are allowed (HTTPS with trusted certificate)
```

## Key Set From a JWKSet Url

```yml
jose:
    key_sets:
        YOUR_KEYSET_ID: # This key set ID must be unique for each key set
            jku: # The key set is loaded from a JWKSet Url ("jku" parameter)
                url: "https://www.googleapis.com/oauth2/v2/certs" # Url of the key set
                is_secured: true # If true, only secured connections are allowed (HTTPS with trusted certificate)
```
