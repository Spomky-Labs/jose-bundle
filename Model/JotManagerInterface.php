<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2016 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace SpomkyLabs\JoseBundle\Model;

interface JotManagerInterface
{
    /**
     * @return \Doctrine\Common\Persistence\ObjectManager
     */
    public function getEntityManager();

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    public function getEntityRepository();

    /**
     * @return \SpomkyLabs\JoseBundle\Model\JotInterface
     */
    public function createJot();

    /**
     * @param \SpomkyLabs\JoseBundle\Model\JotInterface $jot
     *
     * @return self
     */
    public function saveJot(JotInterface $jot);

    /**
     * @param \SpomkyLabs\JoseBundle\Model\JotInterface $jot
     *
     * @return self
     */
    public function removeJot(JotInterface $jot);

    /**
     * @param string $jti
     *
     * @return \SpomkyLabs\JoseBundle\Model\JotInterface|null
     */
    public function getJotById($jti);

    /**
     * @return int
     */
    public function deleteExpired();
}
