<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Form\User;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Talav\CoreBundle\Form\AbstractHelperType;
use Talav\CoreBundle\Form\FormHelper;
use Talav\CoreBundle\Form\Type\CaptchaImageType;
//use Talav\CoreBundle\Service\ApplicationService;
use Talav\CoreBundle\Service\CaptchaImageService;
use Talav\CoreBundle\Validator\Captcha;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Abstract form type for user with a captcha field (if applicable).
 */
abstract class AbstractUserCaptchaType extends AbstractHelperType
{
    /**
     * Constructor.
     */
    //public function __construct(protected CaptchaImageService $service, ApplicationService $application)
    public function __construct(
        protected CaptchaImageService $service,
        #[Autowire('%talav_user.display_captcha%')] private readonly bool $displayCaptcha
    ) {
    }

    public function getBlockPrefix(): string
    {
        return '';
    }

    /**
     * Add form fields.
     *
     * Subclass must call <code>parent::addFormFields($helper);</code> to add
     * the image captcha field (if applicable).
     *
     * @throws \Exception
     */
    protected function addFormFields(FormHelper $helper): void
    {
        if ($this->displayCaptcha) {
            $helper->domain('TalavUserBundle');

            $helper->field('captcha')
                ->label('captcha.label')
                ->constraints(new NotBlank(), new Captcha())
                ->updateOption('image', $this->service->generateImage())
                ->add(CaptchaImageType::class);
        }
    }
}
