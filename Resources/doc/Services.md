Operation Services
==================

This bundle is able to create services to sing, verify, encrypt and decrypt the JWS/JWE you receive.
For each service, you can set as many algorithms as you need.

# Signer Service

The Signer Service will help you to sign claims or a message and create a JWS.

In the following example, we will create two Signer Services:
* The first one (`SIGNER1`) will support `HS256`, `RS256` and `RS512` algorithms
* The second one (`SIGNER2`) will only support `ES384`

```yml
jose:
    signers:
        SIGNER1:
            algorithms:
                - 'HS256'
                - 'RS256'
                - 'RS512'
        SIGNER2:
            algorithms:
                - 'ES384'
```

Now, the aliases `jose.signer.SIGNER1` and `jose.signer.SIGNER2` are available from the container.
You can call these services to sign payloads and create JWS.


# Verifier Service

The Verifier Service will help you to verify JWS signatures.
*Please note that if the JWS contains claims, these claims are not verified. You must use the checker service to verify those claims*.

In the following example, we will create two Verifier Services:
* The first one (`VERIFIER1`) will support `HS256`, `RS256` and `RS512` algorithms
* The second one (`VERIFIER2`) will only support `ES384`

```yml
jose:
    verifiers:
        VERIFIER1:
            algorithms:
                - 'HS256'
                - 'RS256'
                - 'RS512'
        VERIFIER2:
            algorithms:
                - 'ES384'
```

Now, the aliases `jose.verifier.VERIFIER1` and `jose.verifier.VERIFIER2` are available from the container.
You can call these services to verify JWS signatures.

## Create Signer and Verifier Services at Once

If you want to create Signer and Verifier Services with the same algorithms (see example above) at once, you can do it easily using a simple configuration paratemter:

```yml
jose:
    signers:
        SERVICE1:
            algorithms:
                - 'HS256'
                - 'RS256'
                - 'RS512'
            create_verifier: true
        SERVICE2:
            algorithms:
                - 'ES384'
```

Now, the aliases `jose.signer.SERVICE1`, `jose.verifier.SERVICE1` and `jose.verifier.SERVICE2` are available from the container.

# Checker Manager Service

# Encrypter Service

# Decrypter Service


