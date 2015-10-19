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

use Jose\EncrypterInterface;
use Jose\JSONSerializationModes;
use Jose\JWAManagerInterface;
use Jose\JWEInterface;
use Jose\JWKInterface;
use Jose\JWKSetInterface;
use Jose\JWKSetManagerInterface;
use Jose\JWSInterface;
use Jose\JWTInterface;
use Jose\LoaderInterface;
use Jose\SignerInterface;
use SpomkyLabs\Jose\EncryptionInstruction;
use SpomkyLabs\Jose\SignatureInstruction;
use SpomkyLabs\JoseBundle\Model\JotInterface;
use SpomkyLabs\JoseBundle\Model\JotManagerInterface;

class Jose
{
    /**
     * @var \Jose\LoaderInterface
     */
    private $loader;

    /**
     * @var \Jose\SignerInterface
     */
    private $signer;

    /**
     * @var \Jose\EncrypterInterface
     */
    private $encrypter;

    /**
     * @var \Jose\JWKSetManagerInterface
     */
    private $keyset_manager;

    /**
     * @var \Jose\JWAManagerInterface
     */
    private $algorithm_manager;

    /**
     * @var null|\SpomkyLabs\JoseBundle\Model\JotManagerInterface
     */
    private $jot_manager;

    /**
     * @var string
     */
    private $server_name;

    /**
     * @param \Jose\LoaderInterface                                 $loader
     * @param \Jose\SignerInterface                                 $signer
     * @param \Jose\EncrypterInterface                              $encrypter
     * @param \Jose\JWKSetManagerInterface                          $keyset_manager
     * @param \Jose\JWAManagerInterface                             $algorithm_manager
     * @param string                                                $server_name
     * @param \SpomkyLabs\JoseBundle\Model\JotManagerInterface|null $jot_manager
     */
    public function __construct(
        LoaderInterface $loader,
        SignerInterface $signer,
        EncrypterInterface $encrypter,
        JWKSetManagerInterface $keyset_manager,
        JWAManagerInterface $algorithm_manager,
        $server_name,
        JotManagerInterface $jot_manager = null
    )
    {
        $this->loader = $loader;
        $this->signer = $signer;
        $this->encrypter = $encrypter;
        $this->keyset_manager = $keyset_manager;
        $this->algorithm_manager = $algorithm_manager;
        $this->jot_manager = $jot_manager;
        $this->server_name = $server_name;
    }

    /**
     * @return \string[]
     */
    public function getSupportedAlgorithms()
    {
        return $this->algorithm_manager->listAlgorithms();
    }

    /**
     * Load data and try to return a JWSInterface object or a JWEInterface object.
     *
     * @param string                     $input   A string that represents a JSON Web Token message
     * @param \Jose\JWKSetInterface|null $jwk_set If not null, use the key set used to verify or decrypt the input, else this method should use a default keys manager.
     * @param null|string                $detached_payload   If not null, the value must be the detached payload encoded in Base64 URL safe. If the input contains a payload, throws an exception.
     *
     * @return \Jose\JWSInterface|\Jose\JWEInterface|null If the data has been loaded.
     */
    public function load($input, JWKSetInterface $jwk_set = null, $detached_payload = null)
    {
        $result = $this->loader->load($input);
        $loaded = null;
        if (is_array($result)) {
            foreach($result as $temp) {
                $loaded = $this->checkResult($temp, $jwk_set, $detached_payload);
                if (null !== $loaded) {
                    break;
                }
            }
        } else {
            $loaded = $this->checkResult($result, $jwk_set, $detached_payload);
        }

        if (null === $loaded) {
            throw new \InvalidArgumentException('Unable to load the input');
        }

        $this->verify($loaded);

        return $loaded;
    }

    /**
     * Load data and try to return a JWSInterface object, a JWEInterface object or a list of these objects.
     *
     * @param string                     $input   A string that represents a JSON Web Token message
     * @param \Jose\JWKSetInterface|null $jwk_set If not null, use the key set used to verify or decrypt the input, else this method should use a default keys manager.
     * @param null|string                $detached_payload   If not null, the value must be the detached payload encoded in Base64 URL safe. If the input contains a payload, throws an exception.
     *
     * @return \Jose\JWSInterface|\Jose\JWEInterface|null If the data has been loaded.
     */
    protected function checkResult($input, JWKSetInterface $jwk_set = null, $detached_payload = null)
    {
        if ($input instanceof JWSInterface) {
            if (true !== $this->verifySignature($input, $jwk_set, $detached_payload)) {
                return null;
            }
        } elseif ($input instanceof JWEInterface) {
            if (true !== $this->decrypt($input, $jwk_set)) {
                return null;
            }
        }
        return $input;
    }

    /**
     * Load data and try to return a JWSInterface object, a JWEInterface object or a list of these objects.
     * If the result is a JWE, nothing is decrypted and method `decrypt` must be executed
     * If the result is a JWS, no signature is verified and method `verifySignature` must be executed
     *
     * @param \Jose\JWEInterface         $input   A JWE object to decrypt
     * @param \Jose\JWKSetInterface|null $jwk_set If not null, use the key set used to verify or decrypt the input, else this method should use a default keys manager.
     *
     * @return bool Returns true if the JWE has been populated with decrypted values, else false.
     */
    protected function decrypt(JWEInterface &$input, JWKSetInterface $jwk_set = null)
    {
        return $this->loader->decrypt($input, $jwk_set);
    }

    /**
     * Verify the signature of the input.
     * The input must be a valid JWS. This method is usually called after the "load" method.
     *
     * @param \Jose\JWSInterface         $input              A JWS object.
     * @param \Jose\JWKSetInterface|null $jwk_set            If not null, the signature will be verified only using keys in the key set, else this method should use a default keys manager
     * @param null|string                $detached_payload   If not null, the value must be the detached payload encoded in Base64 URL safe. If the input contains a payload, throws an exception.
     *
     * @return bool True if the signature has been verified, else false
     */
    protected function verifySignature(JWSInterface $input, JWKSetInterface $jwk_set = null, $detached_payload = null)
    {
        return $this->loader->verifySignature($input, $jwk_set, $detached_payload);
    }

    /**
     * Verify the claims of the input.
     * This method must verify if claims are valid or not.
     * For example, if the "exp" header is set and the JWT expired, this method will return false.
     *
     * @param \Jose\JWTInterface $input A JWS object.
     *
     * @return bool True if the JWT has been verified, else false
     */
    protected function verify(JWTInterface $input)
    {
        return $this->loader->verify($input);
    }


    /**
     * Sign an input and convert it into JWS JSON (Compact/Flattened) Serialized representation.
     *
     * @param \Jose\JWTInterface|\Jose\JWKInterface|\Jose\JWKSetInterface|string|array $input              A JWKInterface/JWKInterface/JWKSetInterface object
     * @param string                                                                   $serialization      Serialization method. If the argument $keys contains more than one private key and value is JSON_COMPACT_SERIALIZATION or JSON_FLATTENED_SERIALIZATION, the result will be an array of JWT.
     * @param bool                                                                     $detached_signature If true, the payload will be detached and variable $detached_payload will be set
     * @param null|string                                                              $detached_payload   The detached payload encoded in Base64 URL safe
     *
     * @throws \Exception
     *
     * @return string|string[] The JSON (Compact/Flattened) Serialized representation
     */
    public function sign($input, JWKInterface $key, array $protected_header = [], array $unprotected_header = [], $serialization = JSONSerializationModes::JSON_COMPACT_SERIALIZATION, $detached_signature = false, &$detached_payload = null)
    {
        $jot = null;
        if (null !== $this->jot_manager) {
            $jot = $this->jot_manager->createJot();
            $this->populateJti($input, $jot);
        }

        $instruction = new SignatureInstruction();
        $instruction->setKey($key)
            ->setProtectedHeader($protected_header)
            ->setUnprotectedHeader($unprotected_header);

        $signature = $this->signer->sign($input, [$instruction], $serialization, $detached_signature, $detached_payload);

        if (null !== $this->jot_manager) {
            $this->populateData($input, $jot, $signature);
        }

        return $signature;
    }

    /**
     * @param \Jose\JWTInterface|\Jose\JWKInterface|\Jose\JWKSetInterface|string|array $input
     * @param \SpomkyLabs\JoseBundle\Model\JotInterface                                $jot
     */
    protected function populateJti(&$input, JotInterface $jot)
    {
        if(is_array($input)) {
            $input['jti'] = $jot->getJti();
        } elseif ($input instanceof JWTInterface) {
            $payload = $input->getPayload();
            if(is_array($payload)) {
                $payload['jti'] = $jot->getJti();
                $input->setPayload($payload);
            }
        }
    }

    /**
     * @param                                           $input
     * @param \SpomkyLabs\JoseBundle\Model\JotInterface $jot
     * @param                                           $data
     */
    protected function populateData($input, JotInterface &$jot, $data)
    {
        if(is_array($input) || ($input instanceof JWTInterface && is_array($input->getPayload()))) {
            $jot->setData($data);
            $this->jot_manager->saveJot($jot);
        }
    }

    /**
     * Encrypt an input and convert it into a JWE JSON (Compact/Flattened) Serialized representation.
     *
     * To encrypt the input using different algorithms, the "alg" parameter must be set in the unprotected header of the $instruction.
     * Please note that this is not possible when using the algorithms "dir" or "ECDH-ES".
     *
     * @param \Jose\JWTInterface|\Jose\JWKInterface|\Jose\JWKSetInterface|array|string $input                     A JWKInterface/JWKInterface/JWKSetInterface object
     * @param array                                                                    $shared_protected_header   Shared protected headers. If the input is a JWTInterface object, this parameter is merged with the protected header of the input.
     * @param array                                                                    $shared_unprotected_header Shared unprotected headers. If the input is a JWTInterface object, this parameter is merged with the unprotected header of the input.
     * @param string                                                                   $serialization             Serialization method.
     * @param string|null                                                              $aad                       Additional Authentication Data. This parameter is useless if the serialization is JSON_COMPACT_SERIALIZATION.
     *
     * @throws \Exception
     *
     * @return string|string[] The JSON (Compact/Flattened) Serialized representation
     */
    public function encrypt($input, JWKInterface $recipient_key, JWKInterface $sender_key = null, array $shared_protected_header = [], array $shared_unprotected_header = [], array $recipient_unprotected_hearder = [], $serialization = JSONSerializationModes::JSON_COMPACT_SERIALIZATION, $aad = null)
    {
        $jot = null;
        if (null !== $this->jot_manager) {
            $jot = $this->jot_manager->createJot();
            $this->populateJti($input, $jot);
        }

        $instruction = new EncryptionInstruction();
        $instruction->setRecipientKey($recipient_key)
            ->setRecipientUnprotectedHeader($recipient_unprotected_hearder);
        if ($sender_key instanceof JWKInterface) {
            $instruction->setSenderKey($sender_key);
        }

        $encryption =  $this->encrypter->encrypt($input, [$instruction], $shared_protected_header, $shared_unprotected_header, $serialization, $aad);

        if (null !== $this->jot_manager) {
            $jot = $this->jot_manager->createJot();
            $this->populateData($input, $jot, $encryption);
        }

        return $encryption;
    }
}
