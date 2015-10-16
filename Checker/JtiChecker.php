<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2015 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace SpomkyLabs\JoseBundle\Checker;

use Jose\JWTInterface;
use SpomkyLabs\Jose\Checker\CheckerInterface;
use SpomkyLabs\JoseBundle\Model\JotManagerInterface;

class JtiChecker implements CheckerInterface
{
    private $jot_manager;

    /**
     * @param \SpomkyLabs\JoseBundle\Model\JotManagerInterface $jot_manager
     */
    public function __construct(JotManagerInterface $jot_manager)
    {
        $this->jot_manager = $jot_manager;
    }

    /**
     * {@inheritdoc}
     */
    public function checkJWT(JWTInterface $jwt)
    {
        $jti = $jwt->getJWTID();
        if (null !== $jti) {
            $result = $this->jot_manager->getJotById($jti);
            if (null === $result) {
                throw new \Exception('Bad ID');
            }
            if ($result->getData() !== $jwt->getInput()) {
                throw new \Exception('Invalid input.');
            }
        }

        return $this;
    }
}
