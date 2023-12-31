<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Model;

use Talav\CoreBundle\Traits\ExceptionContextTrait;

/**
 * Contains Http client error.
 */
class HttpClientError implements \JsonSerializable, \Stringable
{
    use ExceptionContextTrait;

    /**
     * Constructor.
     *
     * @param int             $code      the error code
     * @param string          $message   the error message
     * @param \Exception|null $exception the optional source exception
     */
    public function __construct(private readonly int $code, private string $message, private readonly ?\Exception $exception = null)
    {
    }

    public function __toString(): string
    {
        return \sprintf('%d. %s', $this->code, $this->message);
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getException(): ?\Exception
    {
        return $this->exception;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @psalm-return array{
     *      result: false,
     *      code: int,
     *      message: string,
     *      exception?: array{
     *              message: string,
     *              code: string|int,
     *              file: string,
     *              line: int,
     *              class: string,
     *              trace: string}
     *     }
     */
    public function jsonSerialize(): array
    {
        $result = [
            'result' => false,
            'code' => $this->code,
            'message' => $this->message,
        ];
        if ($this->exception instanceof \Exception) {
            $result['exception'] = $this->getExceptionContext($this->exception);
        }

        return $result;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }
}
