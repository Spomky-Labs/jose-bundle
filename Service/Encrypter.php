<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2015 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace SpomkyLabs\JoseBundle\Service;

use Jose\Algorithm\JWAManagerInterface;
use Jose\Compression\CompressionManagerInterface;
use Jose\EncrypterInterface;
use Jose\JSONSerializationModes;
use Jose\Payload\PayloadConverterManagerInterface;
use SpomkyLabs\JoseBundle\Model\JotManagerInterface;

/**
 */
final class Encrypter implements EncrypterInterface
{
    /**
     * @var null|\SpomkyLabs\JoseBundle\Model\JotManagerInterface
     */
    private $jot_manager;

    /**
     * @var \Jose\Encrypter
     */
    private $encrypter;

    /**
     * Encrypter constructor.
     *
     * @param \Jose\Algorithm\JWAManagerInterface                   $jwa_manager
     * @param \Jose\Payload\PayloadConverterManagerInterface        $payload_converter_manager
     * @param \Jose\Compression\CompressionManagerInterface         $compression_manager
     * @param \SpomkyLabs\JoseBundle\Model\JotManagerInterface|null $jot_manager
     */
    public function __construct(
        JWAManagerInterface $jwa_manager,
        PayloadConverterManagerInterface $payload_converter_manager,
        CompressionManagerInterface $compression_manager,
        JotManagerInterface $jot_manager = null
    ) {
        $this->jot_manager = $jot_manager;
        $this->encrypter = new \Jose\Encrypter($jwa_manager, $payload_converter_manager, $compression_manager);
    }

    /**
     * {@inheritdoc}
     */
    public function encrypt($input, array $instructions, $serialization, array $shared_protected_header = [], array $shared_unprotected_header = [], $aad = null)
    {
        if (!$this->jot_manager instanceof JotManagerInterface) {
            return $this->encrypter->encrypt($input, $instructions, $serialization, $shared_protected_header, $shared_unprotected_header, $aad);
        }

        if (1 < count($instructions) && $serialization !== JSONSerializationModes::JSON_SERIALIZATION) {
            $result = [];
            foreach($instructions as $instruction) {
                $result[] = $this->encryptData($input, [$instruction], $serialization, $shared_protected_header, $shared_unprotected_header, $aad);
            }

            return $result;
        } else {
            return $this->encryptData($input, $instructions, $serialization, $shared_protected_header, $shared_unprotected_header, $aad);
        }
    }

    /**
     * Encrypt an input and convert it into a JWE JSON (Compact/Flattened) Serialized representation.
     *
     * To encrypt the input using different algorithms, the "alg" parameter must be set in the unprotected header of the $instruction.
     * Please note that this is not possible when using the algorithms "dir" or "ECDH-ES".
     *
     * @param \Jose\Object\JWTInterface|\Jose\Object\JWKInterface|\Jose\Object\JWKSetInterface|array|string $input                     A JWKInterface/JWKInterface/JWKSetInterface object
     * @param \Jose\Object\EncryptionInstructionInterface[]                                                 $instructions              A list of instructions used to encrypt the input
     * @param array                                                                                         $shared_protected_header   Shared protected headers. If the input is a JWTInterface object, this parameter is merged with the protected header of the input.
     * @param array                                                                                         $shared_unprotected_header Shared unprotected headers. If the input is a JWTInterface object, this parameter is merged with the unprotected header of the input.
     * @param string                                                                                        $serialization             Serialization method.
     * @param string|null                                                                                   $aad                       Additional Authentication Data. This parameter is useless if the serialization is JSON_COMPACT_SERIALIZATION.
     *
     * @throws \Exception
     *
     * @return string|string[] The JSON (Compact/Flattened) Serialized representation
     */
    private function encryptData($input, array $instructions, $serialization, array $shared_protected_header = [], array $shared_unprotected_header = [], $aad = null)
    {
        $jot = $this->jot_manager->createJot();
        $shared_protected_header['jti'] = $jot->getJti();

        $jwe = $this->encrypter->encrypt($input, $instructions, $serialization, $shared_protected_header, $shared_unprotected_header, $aad);

        $jot = $jot->withData($jwe);
        $this->jot_manager->saveJot($jot);

        return $jwe;
    }
}
