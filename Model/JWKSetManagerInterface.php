<?php

namespace SpomkyLabs\JoseBundle\Model;

use Jose\JWKSetManagerInterface as Base;

interface JWKSetManagerInterface extends Base
{
    /**
     * @return \SpomkyLabs\JoseBundle\Model\JWKSetInterface
     */
    public function getPublicKeyset();

    /**
     * @return \SpomkyLabs\JoseBundle\Model\JWKSetInterface
     */
    public function getPrivateKeyset();

    /**
     * @param string $kid    The key ID
     * @param bool   $public True if the key to find is public, false if the key is private
     *
     * @return \SpomkyLabs\JoseBundle\Model\JWKInterface|null
     */
    public function findKeyById($kid, $public);
}
