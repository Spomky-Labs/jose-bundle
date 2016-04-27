Configuration
=============

# Keys and Key Sets

Encryption/Decryption and Signature/Verification require keys or key sets to be done.
This bundle is able to load keys and key sets from various sources such as files (encrypted or not), certificates, URLs or values.

When loaded, the keys or key sets are available though services.

Please read [this page](config/keys.md) to know how to load your keys and [this one](config/key_sets.md) for your key sets.

# Signers and Verifiers

The Signers and Verifiers services are used to sign and verify JWS objects.

You can create multiple services depending on your needs. For each service, selected algorithms may be different.
For example, you need a Signer to sign a JWS to be sent to clients using HS512 algorithm only and you need a Verifier
to verify requests from clients that use RS256 or ES384 algorithms.

Please read [this page](config/signers_and_verifiers.md) to know how to create your Singers and Verifiers Services.

# Encrypters and Decrypters

The Encrypters and Decrypters services are used to encrypt and decrypt JWE objects.

Like Signers and Verifiers, yu can create multiple services depending on your needs.
For each service, selected algorithms and compression methods may be different.
