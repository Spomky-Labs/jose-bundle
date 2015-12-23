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

use Base64Url\Base64Url;
use Jose\Algorithm\JWAManagerInterface;
use Jose\Algorithm\Signature\SignatureInterface;
use Jose\Behaviour\HasJWAManager;
use Jose\Behaviour\HasKeyChecker;
use Jose\Behaviour\HasPayloadConverter;
use Jose\JSONSerializationModes;
use Jose\Object\JWKInterface;
use Jose\Object\SignatureInstruction;
use Jose\Object\SignatureInstructionInterface;
use Jose\Payload\PayloadConverterManagerInterface;
use Jose\SignerInterface;
use Jose\Util\Converter;
use SpomkyLabs\JoseBundle\Model\JotInterface;
use SpomkyLabs\JoseBundle\Model\JotManagerInterface;

/**
 */
final class Signer implements SignerInterface
{
    /**
     * @var null|\SpomkyLabs\JoseBundle\Model\JotManagerInterface
     */
    private $jot_manager;

    /**
     * @var \Jose\Signer
     */
    private $signer;

    /**
     * Signer constructor.
     *
     * @param \Jose\Algorithm\JWAManagerInterface                   $jwa_manager
     * @param \Jose\Payload\PayloadConverterManagerInterface        $payload_converter_manager
     * @param \SpomkyLabs\JoseBundle\Model\JotManagerInterface|null $jot_manager
     */
    public function __construct(JWAManagerInterface $jwa_manager,
                                PayloadConverterManagerInterface $payload_converter_manager,
                                JotManagerInterface $jot_manager = null
    ) {
        $this->jot_manager = $jot_manager;
        $this->signer = new \Jose\Signer($jwa_manager, $payload_converter_manager);
    }

    /**
     * {@inheritdoc}
     */
    public function sign($input, array $instructions, $serialization, $detached_signature = false, &$detached_payload = null)
    {
        if (!$this->jot_manager instanceof JotManagerInterface) {
            return $this->signer->sign($input, $instructions, $serialization, $detached_signature, $detached_payload);
        }

        if (1 < count($instructions) && $serialization !== JSONSerializationModes::JSON_SERIALIZATION) {
            $result = [];
            foreach($instructions as $instruction) {
                $result[] = $this->signData($input, [$instruction], $serialization, $detached_signature, $detached_payload);
            }

            return $result;
        } else {
            return $this->signData($input, $instructions, $serialization, $detached_signature, $detached_payload);
        }
    }

    /**
     * Sign an input and convert it into JWS JSON (Compact/Flattened) Serialized representation.
     *
     * @param \Jose\Object\JWKInterface|\Jose\Object\JWKSetInterface|string|array $input              A JWKInterface/JWKInterface/JWKSetInterface object
     * @param \Jose\Object\SignatureInstructionInterface[]                        $instructions       A list of instructions used to sign the input
     * @param string                                                              $serialization      Serialization method. If the argument $keys contains more than one private key and value is JSON_COMPACT_SERIALIZATION or JSON_FLATTENED_SERIALIZATION, the result will be an array of JWT.
     * @param bool                                                                $detached_signature If true, the payload will be detached and variable $detached_payload will be set
     * @param null|string                                                         $detached_payload   The detached payload encoded in Base64 URL safe
     *
     * @throws \Exception
     *
     * @return string|string[] The JSON (Compact/Flattened) Serialized representation
     */
    private function signData($input, array $instructions, $serialization, $detached_signature = false, &$detached_payload = null)
    {
        $jot = $this->jot_manager->createJot();
        $instructions = $this->getNewInstructions($instructions, $jot);

        $jws = $this->signer->sign($input, $instructions, $serialization, $detached_signature, $detached_payload);

        $jot = $jot->withData($jws);
        $this->jot_manager->saveJot($jot);

        return $jws;
    }

    /**
     * @param \Jose\Object\SignatureInstructionInterface[] $instructions A list of instructions used to sign the input
     * @param \SpomkyLabs\JoseBundle\Model\JotInterface $jot

     * @return \Jose\Object\SignatureInstructionInterface[]
     */
    private function getNewInstructions(array $instructions, JotInterface $jot)
    {
        $new_instructions = [];
        foreach($instructions as $instruction) {
            $protected_header = $instruction->getProtectedHeader();
            $protected_header['jti'] = $jot->getJti();

            $new_instructions[] = new SignatureInstruction(
                $instruction->getKey(),
                $protected_header,
                $instruction->getUnprotectedHeader()
            );
        }

        return $new_instructions;
    }
}
