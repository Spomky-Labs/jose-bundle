<?php

namespace SpomkyLabs\JoseBundle\Model;

use Jose\JWSInterface;

class JWS extends JWT implements JWSInterface
{
    protected $input;
    protected $signature;

    public function getInput()
    {
        return $this->input;
    }

    public function setInput($input)
    {
        $this->input = $input;

        return $this;
    }

    public function getSignature()
    {
        return $this->signature;
    }

    public function setSignature($signature)
    {
        $this->signature = $signature;

        return $this;
    }
}
