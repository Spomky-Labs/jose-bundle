<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2016 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace SpomkyLabs\JoseBundle\Helper;
use Assert\Assertion;

/**
 * This helper will help you to create services configuration
 */
final class ConfigurationHelper
{
    /**
     * @param string   $name
     * @param string[] $header_checkers
     * @param string[] $claim_checkers
     *
     * @return array
     */
    public static function getCheckerConfiguration($name, array $header_checkers, array $claim_checkers)
    {
        Assertion::string($name);
        Assertion::notEmpty($name);
        Assertion::allString($header_checkers);
        Assertion::allString($claim_checkers);
        return [
            'jose' => [
                'checkers' => [
                    $name => [
                        'claims' => $claim_checkers,
                        'headers' => $header_checkers,
                    ]
                ]
            ]
        ];
    }

    /**
     * @param string      $name
     * @param string[]    $signature_algorithms
     *
     * @param bool        $create_verifier
     *
     * @return array
     */
    public static function getSignerConfiguration($name, array $signature_algorithms, $create_verifier = false)
    {
        Assertion::string($name);
        Assertion::notEmpty($name);
        Assertion::allString($signature_algorithms);
        Assertion::notEmpty($signature_algorithms);
        Assertion::boolean($create_verifier);
        return [
            'jose' => [
                'signers' => [
                    $name => [
                        'algorithms' => $signature_algorithms,
                        'create_verifier' => $create_verifier,
                    ]
                ]
            ]
        ];
    }

    /**
     * @param string      $name
     * @param string[]    $signature_algorithms
     *
     *
     * @return array
     */
    public static function getVerifierConfiguration($name, array $signature_algorithms)
    {
        Assertion::string($name);
        Assertion::notEmpty($name);
        Assertion::allString($signature_algorithms);
        Assertion::notEmpty($signature_algorithms);
        return [
            'jose' => [
                'verifiers' => [
                    $name => [
                        'algorithms' => $signature_algorithms,
                    ]
                ]
            ]
        ];
    }

    /**
     * @param string      $name
     * @param string[]    $key_encryption_algorithms
     * @param string[]    $content_encryption_algorithms
     * @param string[]    $compression_methods
     *
     * @param bool        $create_decrypter
     *
     * @return array
     */
    public static function getEncrypterConfiguration($name, array $key_encryption_algorithms, array $content_encryption_algorithms, array $compression_methods = ['DEF'], $create_decrypter = false)
    {
        Assertion::string($name);
        Assertion::notEmpty($name);
        Assertion::allString($key_encryption_algorithms);
        Assertion::notEmpty($key_encryption_algorithms);
        Assertion::allString($content_encryption_algorithms);
        Assertion::notEmpty($content_encryption_algorithms);
        Assertion::boolean($create_decrypter);
        return [
            'jose' => [
                'encrypters' => [
                    $name => [
                        'key_encryption_algorithms' => $key_encryption_algorithms,
                        'content_encryption_algorithms' => $content_encryption_algorithms,
                        'compression_methods' => $compression_methods,
                        'create_decrypter' => $create_decrypter,
                    ]
                ]
            ]
        ];
    }

    /**
     * @param string      $name
     * @param string[]    $key_encryption_algorithms
     * @param string[]    $content_encryption_algorithms
     * @param string[]    $compression_methods
     *
     *
     * @return array
     */
    public static function getDecrypterConfiguration($name, array $key_encryption_algorithms, array $content_encryption_algorithms, array $compression_methods = ['DEF'])
    {
        Assertion::string($name);
        Assertion::notEmpty($name);
        Assertion::allString($key_encryption_algorithms);
        Assertion::notEmpty($key_encryption_algorithms);
        Assertion::allString($content_encryption_algorithms);
        Assertion::notEmpty($content_encryption_algorithms);
        return [
            'jose' => [
                'decrypters' => [
                    $name => [
                        'key_encryption_algorithms' => $key_encryption_algorithms,
                        'content_encryption_algorithms' => $content_encryption_algorithms,
                        'compression_methods' => $compression_methods,
                    ]
                ]
            ]
        ];
    }

    /**
     * @param string      $name
     * @param string      $signer
     * @param string|null $encrypter
     *
     * @return array
     */
    public static function getJWTCreatorConfiguration($name, $signer, $encrypter = null)
    {
        Assertion::string($name);
        Assertion::notEmpty($name);
        Assertion::string($signer);
        Assertion::notEmpty($signer);
        Assertion::nullOrString($encrypter);
        return [
            'jose' => [
                'jwt_creators' => [
                    $name => [
                        'signer' => $signer,
                        'encrypter' => $encrypter,
                    ]
                ]
            ]
        ];
    }

    /**
     * @param string      $name
     * @param string      $verifier
     * @param string      $checker
     * @param string|null $decrypter
     *
     *
     * @return array
     */
    public static function getJWTLoaderConfiguration($name, $verifier, $checker, $decrypter = null)
    {
        Assertion::string($name);
        Assertion::notEmpty($name);
        Assertion::string($verifier);
        Assertion::notEmpty($verifier);
        Assertion::string($checker);
        Assertion::notEmpty($checker);
        Assertion::nullOrString($decrypter);
        return [
            'jose' => [
                'jwt_loaders' => [
                    $name => [
                        'verifier' => $verifier,
                        'checker' => $checker,
                        'decrypter' => $decrypter,
                    ]
                ]
            ]
        ];
    }
}
