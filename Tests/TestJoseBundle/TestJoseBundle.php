<?php

namespace SpomkyLabs\TestJoseBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use SpomkyLabs\TestJoseBundle\DependencyInjection\TestExtension;

class TestJoseBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new TestExtension('sl_jose_test');
    }
}
