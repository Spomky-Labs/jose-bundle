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
use Jose\JWKSetInterface;
use Jose\JWKSetManagerInterface;
use Jose\JWSInterface;
use Jose\JWTInterface;
use Jose\LoaderInterface;
use Jose\SignerInterface;
use SpomkyLabs\JoseBundle\Model\JotInterface;
use SpomkyLabs\JoseBundle\Model\JotManagerInterface;

class Jose implements JoseInterface
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
     * @var null|\SpomkyLabs\JoseBundle\Model\JotManagerInterface
     */
    private $jot_manager;

    /**
     * @var string
     */
    private $server_name;

    /**
     * @param \Jose\JWKSetManagerInterface                          $keyset_manager
     * @param string                                                $server_name
     */
    public function __construct(
        JWKSetManagerInterface $keyset_manager,
        $server_name
    )
    {
        $this->keyset_manager = $keyset_manager;
        $this->server_name = $server_name;
    }

    /**
     * {@inheritdoc}
     */
    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * {@inheritdoc}
     */
    public function setLoader(LoaderInterface $loader)
    {
        $this->loader = $loader;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSigner()
    {
        return $this->signer;
    }

    /**
     * {@inheritdoc}
     */
    public function setSigner(SignerInterface $signer)
    {
        $this->signer = $signer;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getEncrypter()
    {
        return $this->encrypter;
    }

    /**
     * {@inheritdoc}
     */
    public function setEncrypter(EncrypterInterface $encrypter)
    {
        $this->encrypter = $encrypter;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getJotManager()
    {
        return $this->jot_manager;
    }

    /**
     * {@inheritdoc}
     */
    public function setJotManager($jot_manager)
    {
        if (null !== $jot_manager && !$jot_manager instanceof JotManagerInterface) {
            throw new \InvalidArgumentException('Invalid argument: must be null or an instance of SpomkyLabs\JoseBundle\Model\JotManagerInterface');
        }
        $this->jot_manager = $jot_manager;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function checkJWT(&$input, JWKSetInterface $keyset = null, $detached_payload = null)
    {
        if ($input instanceof JWSInterface) {
            if (true !== $this->getLoader()->verifySignature($input, $keyset, $detached_payload)) {
                throw new \InvalidArgumentException('Unable to verify the signature of the input.');
            }
        } elseif ($input instanceof JWEInterface) {
            if (true !== $this->getLoader()->decrypt($input, $keyset)) {
                throw new \InvalidArgumentException('Unable to decrypt the input');
            }
        } else {
            throw new \InvalidArgumentException('Unsupported input.');
        }

        $this->getLoader()->verify($input);

        return $input;
    }

    /**
     * {@inheritdoc}
     */
    public function encrypt($input, array $instructions, array $shared_protected_header = [], array $shared_unprotected_header = [], $serialization = JSONSerializationModes::JSON_COMPACT_SERIALIZATION, $aad = null)
    {
        $jot = null;
        if (null !== $this->getJotManager()) {
            $jot = $this->getJotManager()->createJot();
            $this->populateJti($input, $jot);
        }

        $encrypted = $this->encrypter->encrypt($input, $instructions, $shared_protected_header, $shared_unprotected_header, $serialization, $aad);

        if (null !== $this->getJotManager()) {
            $this->populateData($input, $jot, $encrypted);
        }

        return $encrypted;
    }

    /**
     * {@inheritdoc}
     */
    public function decrypt(JWEInterface &$input, JWKSetInterface $jwk_set = null)
    {
        return $this->loader->decrypt($input, $jwk_set);
    }

    /**
     * {@inheritdoc}
     */
    public function verifySignature(JWSInterface $input, JWKSetInterface $jwk_set = null, $detached_payload = null)
    {
        return $this->loader->verifySignature($input, $jwk_set, $detached_payload);
    }

    /**
     * {@inheritdoc}
     */
    public function verify(JWTInterface $input)
    {
        return $this->loader->verify($input);
    }

    /**
     * {@inheritdoc}
     */
    public function sign($input, array $instructions, $serialization = JSONSerializationModes::JSON_COMPACT_SERIALIZATION, $detached_signature = false, &$detached_payload = null)
    {
        $jot = null;
        if (null !== $this->getJotManager()) {
            $jot = $this->getJotManager()->createJot();
            $this->populateJti($input, $jot);
        }

        $signature = $this->signer->sign($input, $instructions, $serialization, $detached_signature, $detached_payload);

        if (null !== $this->getJotManager()) {
            $this->populateData($input, $jot, $signature);
        }

        return $signature;
    }

    /**
     * {@inheritdoc}
     */
    public function load($input)
    {
        return $this->loader->load($input);
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
}
