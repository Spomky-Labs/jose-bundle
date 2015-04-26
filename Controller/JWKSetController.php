<?php

namespace SpomkyLabs\JoseBundle\Controller;

use SpomkyLabs\JoseBundle\Model\JWKSetManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class JWKSetController
{
    protected $jwkset_manager;

    public function __construct(JWKSetManagerInterface $jwkset_manager)
    {
        $this->jwkset_manager = $jwkset_manager;
    }

    public function getPublicKeysetAction()
    {
        $jwkset = $this->getJWKSetManager()->getPublicKeyset();
        if (is_null($jwkset)) {
            $jwkset = array();
        }

        return new Response(
            json_encode($jwkset),
            200,
            array('Content-Type' => 'application/json; charset=UTF-8')
        );
    }

    public function getPublicKeyAction($id)
    {
        $jwk = $this->getJWKSetManager()->findKeyById($id, true);
        if (is_null($jwk)) {
            throw new NotFoundHttpException(sprintf('The public key with ID "%s" does not exist.', $id));
        }

        return new Response(
            json_encode($jwk),
            200,
            array('Content-Type' => 'application/json; charset=UTF-8')
        );
    }

    protected function getJWKSetManager()
    {
        return $this->jwkset_manager;
    }
}
