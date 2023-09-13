<?php

declare(strict_types=1);

namespace Talav\CoreBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class TalavCoreBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

}
