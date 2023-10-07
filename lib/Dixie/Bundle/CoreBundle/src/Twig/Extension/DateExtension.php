<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Twig\Extension;

use DateTime;
use DateTimeInterface;
use Twig\Extension\AbstractExtension as Extension;
use Twig\TwigFilter;

class DateExtension extends Extension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('diff_date', $this->diffDate(...)),
            new TwigFilter('stats_diff_date', $this->statsDiffDate(...)),
            new TwigFilter('datetime', $this->getDateTime(...)),
        ];
    }

    public function getDateTime($string): DateTime
    {
        return new DateTime($string);
    }

    public function diffDate(DateTimeInterface $date): string
    {
        return $this->statsDiffDate($date)['full'];
    }

    /**
     * @return string[]
     *
     * @psalm-return array{short: string, long: string, full: string}
     */
    public function statsDiffDate(DateTimeInterface $date): array
    {
        $diff = $date->diff(new DateTime());

        if ($diff->y > 0) { // Years
            return [
                'short' => sprintf('%d year%s', $diff->y, $diff->y > 1 ? 's' : ''),
                'long' => sprintf('%d year%s', $diff->y, $diff->y > 1 ? 's' : ''),
                'full' => sprintf('%d year%s ago', $diff->y, $diff->y > 1 ? 's' : ''),
            ];
        } elseif ($diff->m > 0) { // Mois
            return [
                'short' => sprintf('%d months', $diff->m),
                'long' => sprintf('%d months', $diff->m),
                'full' => sprintf('%d months ago', $diff->m),
            ];
        } elseif ($diff->d > 0) { // Jours
            return [
                'short' => sprintf('%d j', $diff->d),
                'long' => sprintf('%d days', $diff->d),
                'full' => sprintf('%d days ago', $diff->d),
            ];
        } elseif ($diff->h > 0) { // Heures
            return [
                'short' => sprintf('%d h', $diff->h),
                'long' => sprintf('%d hour%s', $diff->h, $diff->h > 1 ? 's' : ''),
                'full' => sprintf('%d hour%s ago', $diff->h, $diff->h > 1 ? 's' : ''),
            ];
        } elseif ($diff->i > 0) { // Minutes
            return [
                'short' => sprintf('%d min', $diff->i),
                'long' => sprintf('%d minute%s', $diff->i, $diff->i > 1 ? 's' : ''),
                'full' => sprintf('%d minute%s ago', $diff->i, $diff->i > 1 ? 's' : ''),
            ];
        } elseif ($diff->s > 30) { // Secondes
            return [
                'short' => sprintf('%d s', $diff->s),
                'long' => sprintf('%d second%s', $diff->s, $diff->s > 1 ? 's' : ''),
                'full' => sprintf('%d second%s ago', $diff->s, $diff->s > 1 ? 's' : ''),
            ];
        }

        return [
            'short' => '0 s',
            'long' => "just now",
            'full' => "Just now",
        ];
    }
}
