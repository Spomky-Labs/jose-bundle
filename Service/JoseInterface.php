<?php

namespace SpomkyLabs\JoseBundle\Service;

interface JoseInterface
{
    /**
     * @param $input
     *
     * @return \Jose\JWEInterface|\Jose\JWEInterface[]|\Jose\JWSInterface|\Jose\JWSInterface[]|null
     */
    public function load($input);

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
    public function signAndEncrypt($input, $signature_key, $recipient_key, $sender_key, array $signature_protected_header = array(), array $encryption_protected_header = array());

    /**
     * @param       $input
     * @param       $recipient_key
     * @param null  $sender_key
     * @param array $protected_header
     *
     * @return string
     */
    public function encrypt($input, $recipient_key, $sender_key = null, array $protected_header = array());

    /**
     * @param array|\SpomkyLabs\JoseBundle\Model\JWKSetInterface|\SpomkyLabs\JoseBundle\Model\JWKSetInterface $input
     * @param string                                                                                          $key
     * @param array                                                                                           $protected_header
     *
     * @return string
     */
    public function sign($input, $key, array $protected_header = array());
}
