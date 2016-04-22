How to use
==========

# Configuration

## Load Your Keys And Key Sets

Signature/Verification and Encryption/Decryption need keys to be performed.
The first step is to configure your keys in the configuration file.

Please read [the dedicated page](Keys.md) to know how to load your keys and key sets.

## Operation Services

This bundle will help you to create operation services that exactly fit on your needs.

For example, you may need to encrypt/decrypt JWE using `A256KW` and `A256GCM` algorithms and want to sign/verify JWS using `HS256` and `PS512` algorithms.
For internal communications, you just sign/verify JWS using `RS384`.

Read [the dedicated page](Operation.md) to know how to perform all these operations.
