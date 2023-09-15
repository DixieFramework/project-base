<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Service;

/**
 * Service to get random words.
 */
class DictionaryService
{
    private const DICTIONARY = [
        'Accointance',
        'Amphigouri',
        'Anonchalir',
        'Barbiturique',
        'Belluaire',
        'Binaural',
        'Brouillamini',
        'Cacochyme',
        'Caligineux',
        'Cauteleux',
        'Clopinette',
        'Coquecigrue',
        'Cosmogonie',
        'Crapoussin',
        'Damasquiner',
        'Difficultueux',
        'Dipsomanie',
        'Dodeliner',
        'Engoulevent',
        'Ergastule',
        'Escarpolette',
        'Essoriller',
        'Falarique',
        'Flavescent',
        'Forlancer',
        'Galimatias',
        'Gnognotte',
        'Gracile',
        'Halieutique',
        'Harmattan',
        'Hypergamie',
        'Hypnagogique',
        'Illuminisme',
        'Immarcescible',
        'Impavide',
        'Incarnadin',
        'Jactance',
        'Janotisme',
        'Leptosome',
        'Lustrine',
        'Margoulin',
        'Mignardise',
        'Myoclonie',
        'Nonobstant',
        'Nitescence',
        'Obombrer',
        'Objurgation',
        'Odalisque',
        'Palinodie',
        'Panoptique',
        'Parangon',
        'Petrichor',
        'Pusillanime',
        'Ratiociner',
        'Rubigineux',
        'Smaragdin',
        'Soliflore',
        'Sophisme',
        'Stochastique',
        'Thaumaturge',
        'Truchement',
        'Vergogne',
        'Vertugadin',
        'Zinzinuler',
    ];

    /**
     * Gets a random word.
     */
    public function getRandomWord(): string
    {
        return \strtoupper(self::DICTIONARY[\array_rand(self::DICTIONARY)]);
    }
}
