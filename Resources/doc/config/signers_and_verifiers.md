Signers and Verifiers Services
==============================

# Signers

A Signer is a service that provides functions to sign payloads according to the headers (protected or unprotected) and private or shared keys.

Each Signer you create is available as a service you can inject in your own services or use from the container. It is allowed to use a set of algorithms you explicitly defined.

In the following example, we create two Signers. They will be available through `jose.signer.SIGNER1` and `jose.signer.SIGNER2` respectively:

```yml
jose:
    signers:
        SIGNER1: # ID of the Signer. Must be unique
            algorithms: # A list of algorithms
                - 'HS256'
                - 'HS384'
                - 'HS512'
        SIGNER2: # ID of the Signer. Must be unique
            algorithms: # A list of algorithms
                - 'RS256'
                - 'RS512'
                - 'PS256'
                - 'PS512'
```

Now you will be able to sign payloads (claims or messages) using these services:

```php
use Jose\Factory\JWSFactory;

// We get the key and the signer (we suppose that MY_KEY1 is a valid key)
$key = $container->get('jose.key.MY_KEY1');
$signer = $container->get('jose.signer.SIGNER1');

// The payload to sign
$payload = 'Hello World!';

// We create a JWS object
$jws = JWSFactory::createJWS($payload);

// We set the information for the signature
$jws->addSignatureInformation(
    $key,
    [
        'alg' => 'HS512',
    ]
);

// We sign it
$signer->sign($jws);

//We can get the Compact, Flattened or General Serialization Representation of that JWS
// 0 is the signature index (the first signature in this case)
$jws->toCompactJSON(0);
$jws->toFlattenedJSON(0);
$jws->toJSON();
```

# Verifiers

A Verifier is a service that provides functions to verify JWS you received using public or shared keys.

As Signers, each Verifier you create is available as a service you can inject in your own services or use from the container. It is allowed to use a set of algorithms you explicitly defined.

In the following example, we create two Verifiers. They will be available through `jose.verifier.VERFIER1` and `jose.verifier.VERFIER2` respectively:

```yml
jose:
    verifiers:
        VERFIER1: # ID of the Verifier. Must be unique
            algorithms: # A list of algorithms
                - 'HS256'
                - 'HS384'
                - 'HS512'
        VERFIER2: # ID of the Verifier. Must be unique
            algorithms: # A list of algorithms
                - 'RS256'
                - 'RS512'
                - 'PS256'
                - 'PS512'
```

Now you will be able to verify JWS using these services. Please note a Verifier verifies the signatures, not the claims of the JWS.

```php
// To be written
```
