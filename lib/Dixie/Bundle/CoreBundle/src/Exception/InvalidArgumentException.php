<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Exception;

class InvalidArgumentException extends \Exception
{
    public function action()
    {
        return array('statusCode' => 2, 'message' => $this->getMessage());
    }
}
