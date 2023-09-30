<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AgeExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('ageMonth', [$this, 'calculateAgeMonths']),
            new TwigFilter('ageYear', [$this, 'calculateAgeYear']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('ageMonth', [$this, 'calculateAgeMonths']),
            new TwigFunction('ageYear', [$this, 'calculateAgeYear']),
        ];
    }

    /**
     * Return the age for a given birthdate
     *
     * @param \DateTime $birthdate
     *
     * @return int
     */
    public function calculateAgeYear(\DateTime $birthdate)
    {
        $now = new \DateTime();
        $interval = $now->diff($birthdate);

        return $interval->y;
    }

    /**
     * Return the age for a given birthdate
     *
     * @param \DateTime $birthdate
     *
     * @return int
     */
    public function calculateAgeMonths(\DateTime $birthdate)
    {
        $now = new \DateTime();
        $interval = $now->diff($birthdate);

        return $interval->m;
    }
}
