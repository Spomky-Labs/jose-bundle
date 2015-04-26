<?php

namespace SpomkyLabs\JoseBundle\Model;

use Jose\JWKManagerInterface as Base;

interface JWKManagerInterface extends Base
{
    /**
     * @param string $kid    The key ID
     * @param bool   $public True if the key to find is public, false if the key is private
     *
     * @return \SpomkyLabs\JoseBundle\Model\JWKInterface|null
     */
    public function findKeyById($kid, $public);

    /**
     * @param string $certificate A certificate or the path to a certificate
     * @param string $passphrase  Password if certificate is protected
     *
     * @return \SpomkyLabs\JoseBundle\Model\JWKInterface
     */
    public function loadKeyFromX509Certificate($certificate, $passphrase = null);

    /**
     * @param string $certificate A certificate or the path to a certificate
     *
     * @return \SpomkyLabs\JoseBundle\Model\JWKInterface
     */
    public function loadKeyFromECCCertificate($certificate);

    /**
     * @param array $values Values of the key
     *
     * @return \SpomkyLabs\JoseBundle\Model\JWKInterface
     */
    public function loadKeyFromValues(array $values);
}
