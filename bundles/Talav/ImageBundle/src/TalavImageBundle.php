<?php

declare(strict_types=1);

namespace Talav\ImageBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class TalavImageBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

}
