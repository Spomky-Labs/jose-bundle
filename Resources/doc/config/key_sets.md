Key Sets
========

# From a List of Keys

The following example shows you how to load a key set from a list of keys.

```yml
jose:
    key_sets:
        keyset_id: # ID of the key set. When loaded, the service "jose.key_set.keyset_id" will be created
            keys: # Type of key set. In this case, the key set is created using keys previously loaded.
                id:
                    - key_id1
                    - key_id2
                    - key_id3
```

# From a JWKSet

The following example shows you how to load a key set from a serialized JWKSet.

```yml
jose:
    key_sets:
        keyset_id: # ID of the key set. When loaded, the service "jose.key_set.keyset_id" will be created
            jwkset: # Type of key set. In this case, the key set is created from a serialized JWKSet.
                value: '{"keys":[{"kty":"EC","crv":"P-256","x":"f83OJ3D2xF1Bg8vub9tLe1gHMzV76e8Tus9uPHvRVEU","y":"x_FEzRu9m36HLN_tue659LNpXW6pCyStikYjKIWI5a0","use":"sign","key_ops":["sign"],"alg":"ES256","kid":"0123456789"},{"kty":"EC","crv":"P-256","x":"f83OJ3D2xF1Bg8vub9tLe1gHMzV76e8Tus9uPHvRVEU","y":"x_FEzRu9m36HLN_tue659LNpXW6pCyStikYjKIWI5a0","d":"jpsQnnGQmL-YBIffH1136cspYG6-0iY7X1fCE9-E9LI","use":"sign","key_ops":["verify"],"alg":"ES256","kid":"9876543210"}]}'
```

# From a JKU (JSON Wek Key URL)

The following example shows you how to load a key set from an URL that contains keys serialized into JWKSet.

```yml
jose:
    key_sets:
        keyset_id: # ID of the key set. When loaded, the service "jose.key_set.keyset_id" will be created
            jku: # Type of key. In this case, the key set is created from a serialized JWK.
                url: "https://www.googleapis.com/oauth2/v2/certs"
                is_secured: true # If false, unsecured connections are allowed. Default is true
```

# From a X5U (X509 Certificates URL)

The following example shows you how to load a key set from an URL that contains certificates.

```yml
jose:
    key_sets:
        keyset_id: # ID of the key set. When loaded, the service "jose.key_set.keyset_id" will be created
            x5u: # Type of key. In this case, the key set is created from a serialized JWK.
                url: "https://www.googleapis.com/oauth2/v1/certs"
                is_secured: true # If false, unsecured connections are allowed. Default is true
```
