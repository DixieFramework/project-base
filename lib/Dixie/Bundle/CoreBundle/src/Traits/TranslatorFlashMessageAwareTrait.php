<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Traits;

/**
 * A combination of the translator trait and the flash message trait to add translated flash messages.
 */
trait TranslatorFlashMessageAwareTrait
{
    use FlashMessageAwareTrait;
    use TranslatorAwareTrait;

    /**
     * Add a translated error message to the session flash bag.
     *
     * @param string  $id         the message id (may also be an object that can be cast to string)
     * @param array   $parameters an array of parameters for the message
     * @param ?string $domain     the domain for the message or null to use the default
     * @param ?string $locale     the locale or null to use the default
     *
     * @return string the translated message
     *
     * @throws \InvalidArgumentException If the locale contains invalid characters
     */
    protected function errorTrans(string $id, array $parameters = [], string $domain = null, string $locale = null): string
    {
        $message = $this->trans($id, $parameters, $domain, $locale);
        $this->error($message);

        return $message;
    }

    /**
     * Add a translated information message to the session flash bag.
     *
     * @param string  $id         the message id (may also be an object that can be cast to string)
     * @param array   $parameters an array of parameters for the message
     * @param ?string $domain     the domain for the message or null to use the default
     * @param ?string $locale     the locale or null to use the default
     *
     * @return string the translated message
     *
     * @throws \InvalidArgumentException If the locale contains invalid characters
     */
    protected function infoTrans(string $id, array $parameters = [], string $domain = null, string $locale = null): string
    {
        $message = $this->trans($id, $parameters, $domain, $locale);
        $this->info($message);

        return $message;
    }

    /**
     * Add a translated success message to the session flash bag.
     *
     * @param string  $id         the message id (may also be an object that can be cast to string)
     * @param array   $parameters an array of parameters for the message
     * @param ?string $domain     the domain for the message or null to use the default
     * @param ?string $locale     the locale or null to use the default
     *
     * @return string the translated message
     *
     * @throws \InvalidArgumentException If the locale contains invalid characters
     */
    protected function successTrans(string $id, array $parameters = [], string $domain = null, string $locale = null): string
    {
        $message = $this->trans($id, $parameters, $domain, $locale);
        $this->success($message);

        return $message;
    }

    /**
     * Add a translated warning message to the session flash bag.
     *
     * @param string  $id         the message id (may also be an object that can be cast to string)
     * @param array   $parameters an array of parameters for the message
     * @param ?string $domain     the domain for the message or null to use the default
     * @param ?string $locale     the locale or null to use the default
     *
     * @return string the translated message
     *
     * @throws \InvalidArgumentException If the locale contains invalid characters
     */
    protected function warningTrans(string $id, array $parameters = [], string $domain = null, string $locale = null): string
    {
        $message = $this->trans($id, $parameters, $domain, $locale);
        $this->warning($message);

        return $message;
    }
}
