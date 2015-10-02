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
use Jose\LoaderInterface;
use Jose\SignerInterface;
use SpomkyLabs\Jose\EncryptionInstruction;
use SpomkyLabs\Jose\SignatureInstruction;
use Jose\JWKInterface;
use Jose\JWKSetInterface;
use SpomkyLabs\JoseBundle\Model\JWKSetManagerInterface;
use Symfony\Component\Routing\RouterInterface;

class Jose implements  JoseInterface
{
    /**
     * @var \Jose\SignerInterface
     */
    protected $signer;

    /**
     * @var \Jose\EncrypterInterface
     */
    protected $encrypter;

    /**
     * @var \Jose\LoaderInterface
     */
    protected $loader;

    /**
     * @var \SpomkyLabs\JoseBundle\Model\JWKSetManagerInterface
     */
    protected $jwkset_manager;

    /**
     * @var array
     */
    protected $configuration;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    protected $server_name;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    protected $router;

    public function __construct(
        LoaderInterface $loader,
        SignerInterface $signer,
        EncrypterInterface $encrypter,
        JWKSetManagerInterface $jwkset_manager,
        RouterInterface $router,
        array $configuration,
        $server_name
    ) {
        $this->loader = $loader;
        $this->signer = $signer;
        $this->encrypter = $encrypter;
        $this->jwkset_manager = $jwkset_manager;
        $this->configuration = $configuration;
        $this->server_name = $server_name;
        $this->router = $router;
    }

    /**
     * @return \Symfony\Component\Routing\RouterInterface
     */
    protected function getRouter()
    {
        return $this->router;
    }

    /**
     * @return \Jose\LoaderInterface
     */
    protected function getLoader()
    {
        return $this->loader;
    }

    /**
     * @return \Jose\SignerInterface
     */
    protected function getSigner()
    {
        return $this->signer;
    }

    /**
     * @return \Jose\EncrypterInterface
     */
    protected function getEncrypter()
    {
        return $this->encrypter;
    }

    /**
     * @return \SpomkyLabs\JoseBundle\Model\JWKSetManagerInterface
     */
    protected function getJWSetKManager()
    {
        return $this->jwkset_manager;
    }

    /**
     * @return array
     */
    protected function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @return string
     */
    protected function getServerName()
    {
        return $this->server_name;
    }

    /**
     * @param       $input
     * @param       $signature_key
     * @param       $recipient_key
     * @param       $sender_key
     * @param array $signature_protected_header
     * @param array $encryption_protected_header
     *
     * @return string
     */
    public function signAndEncrypt($input, $signature_key, $recipient_key, $sender_key, array $signature_protected_header = [], array $encryption_protected_header = [])
    {
        $signed = $this->sign($input, $signature_key, $signature_protected_header);
        $encrypted = $this->encrypt($signed, $recipient_key, $sender_key, array_merge($encryption_protected_header, [
            'cty' => 'JWT',
        ]));

        return $encrypted;
    }

    /**
     * @param       $input
     * @param       $recipient_key
     * @param null  $sender_key
     * @param array $protected_header
     *
     * @return string
     */
    public function encrypt($input, $recipient_key, $sender_key = null, array $protected_header = [])
    {
        //checkInput()
        $instruction = new EncryptionInstruction();

        //Get key
        $recipient_jwk = $this->getJWSetKManager()->findKeyById($recipient_key, true);
        $sender_jwk = $this->getJWSetKManager()->findKeyById($sender_key, false);
        if (null === $recipient_jwk) {
            throw new \InvalidArgumentException(sprintf('The public key with key ID "%s" does not exist.', $recipient_key));
        }

        //Prepare header values
        $additional_protected_header = $this->prepareHeaders($recipient_jwk);
        $protected_header = array_merge($protected_header, $additional_protected_header);
        if (is_array($input)) {
            $additional_claims = $this->prepareClaims();
            $input = array_merge($input, $additional_claims);
        } elseif ($input instanceof JWKInterface) {
            //Replicate 'aud', 'iss' and 'sub' in header
            $protected_header['cty'] = 'jwk+json';
        } elseif ($input instanceof JWKSetInterface) {
            //Replicate 'aud', 'iss' and 'sub' in header
            $protected_header['cty'] = 'jwkset+json';
        }

        $instruction->setRecipientKey($recipient_jwk);
        if (null !== $sender_jwk) {
            $instruction->setSenderKey($sender_jwk);
        }

        return $this->getEncrypter()->encrypt($input, [$instruction], $protected_header);
    }

    /**
     * @param array|\Jose\JWKSetInterface|\Jose\JWKSetInterface $input
     * @param string                                            $key
     * @param array                                             $protected_header
     *
     * @return string
     */
    public function sign($input, $key, array $protected_header = [])
    {
        //checkInput()
        $instruction = new SignatureInstruction();

        //Get key
        $private_jwk = $this->getJWSetKManager()->findKeyById($key, false);
        $public_jwk = $this->getJWSetKManager()->findKeyById($key, true);
        if (null === $private_jwk) {
            throw new \InvalidArgumentException(sprintf('The private key with key ID "%s" does not exist.', $key));
        }
        if (null === $public_jwk) {
            throw new \InvalidArgumentException(sprintf('The public key with key ID "%s" does not exist.', $key));
        }

        //Prepare header values
        $additional_protected_header = $this->prepareHeaders($public_jwk);
        $protected_header = array_merge($protected_header, $additional_protected_header);
        if (is_array($input)) {
            $additional_claims = $this->prepareClaims();
            $input = array_merge($input, $additional_claims);
        } elseif ($input instanceof JWKInterface) {
            //Replicate 'aud', 'iss' and 'sub' in header
            $protected_header['cty'] = 'jwk+json';
        } elseif ($input instanceof JWKSetInterface) {
            //Replicate 'aud', 'iss' and 'sub' in header
            $protected_header['cty'] = 'jwkset+json';
        }

        $instruction->setKey($private_jwk);
        $instruction->setProtectedHeader($protected_header);

        return $this->getSigner()->sign($input, [$instruction]);
    }

    protected function prepareHeaders(JWKInterface $jwk)
    {
        $headers = [
            'typ' => 'JWT',
        ];
        foreach ($this->getConfiguration()['headers'] as $key => $value) {
            switch ($key) {
                case 'jku':
                    if (true === $value) {
                        $headers[$key] = $this->getRouter()->generate('jose_jwkset_endpoint', [], true);
                    }
                    break;
                case 'jwk':
                    if (true === $value) {
                        $headers[$key] = $jwk;
                    }
                    break;
                case 'kid':
                    if (true === $value) {
                        $headers[$key] = $jwk->getKeyID();
                    }
                    break;
                case 'x5c':
                    if (true === $value && null !== $jwk->getX509CertificateChain()) {
                        $headers[$key] = $jwk->getX509CertificateChain();
                    }
                    break;
                case 'x5t':
                    if (true === $value && null !== $jwk->getX509CertificateSha1Thumbprint()) {
                        $headers[$key] = $jwk->getX509CertificateSha1Thumbprint();
                    }
                    break;
                case 'x5t#256':
                    if (true === $value && null !== $jwk->getX509CertificateSha256Thumbprint()) {
                        $headers[$key] = $jwk->getX509CertificateSha256Thumbprint();
                    }
                    break;
                case 'crit':
                    if (!empty($value)) {
                        $headers[$key] = $value;
                    }
                    break;
            }
        }

        return $headers;
    }

    protected function prepareClaims()
    {
        $claims = [];
        foreach ($this->getConfiguration()['claims'] as $key => $value) {
            switch ($key) {
                case 'iss':
                    if (true === $value) {
                        $claims[$key] = $this->getServerName();
                    }
                    break;
                case 'nbf':
                case 'iat':
                    if (true === $value) {
                        $claims[$key] = time();
                    }
                    break;
                case 'lifetime':
                    if (null !== $value) {
                        $lifetime = new \DateTime(sprintf('now +%s', $value));
                        $claims['exp'] = $lifetime->getTimestamp();
                    }
                    break;
            }
        }

        return $claims;
    }
}
