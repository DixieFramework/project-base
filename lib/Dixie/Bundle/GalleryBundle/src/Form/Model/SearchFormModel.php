<?php

declare(strict_types=1);

namespace Talav\GalleryBundle\Form\Model;

use AnthonyMartin\GeoLocation\GeoPoint;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class SearchFormRequest.
 *
 * @SuppressWarnings(PHPMD)
 */
class SearchFormModel
{
    /**
     * @var bool
     */
    public $showOptions = false;

    /** @var int */
    public $min_age = 0;

    /** @var int */
    public $max_age = 120;

    /** @var string */
    public $gender;

	/** @var string */
	public $keywords;

    /** @var int */
    public $direction = Criteria::DESC;

    /** @var int */
    public $items = 20;

    public function overrideFromRequest(Request $request)
    {
        return self::fillObjectFromRequest($this, $request);
    }

    public static function fromRequest(Request $request)
    {
        $searchFormRequest = new self();

        return self::fillObjectFromRequest($searchFormRequest, $request);
    }

    private static function fillObjectFromRequest(self $searchFormRequest, Request $request): self
    {
        $data = [];
        if ($request->query->has('tiny')) {
            $data = $request->query->get('tiny');
        }
        if ($request->query->has('home')) {
            $data = $request->query->get('home');
        }
        if ($request->query->has('search')) {
            $data = $request->query->get('search');
        }
        if ($request->query->has('map')) {
            $data = $request->query->get('map');
        }

        if (empty($data)) {
            // if no data was given return a default object
            return $searchFormRequest;
        }

        $searchFormRequest->keywords = self::get($data, 'keywords', '');
        $searchFormRequest->page = $request->query->get('page', 1);
        $searchFormRequest->min_age = self::get($data, 'min_age', null);
        $searchFormRequest->max_age = self::get($data, 'max_age', null);
        $searchFormRequest->gender = self::get($data, 'gender', null);
        $searchFormRequest->direction = self::get($data, 'direction', Criteria::ASC);
        $searchFormRequest->items = self::get($data, 'items', 10);
        $searchFormRequest->showOptions = self::get($data, 'show_options', false);

        return $searchFormRequest;
    }

    public static function determineValidationGroups(FormInterface $form)
    {
        $data = $form->getData();
        $showOnMap = (bool) ($data->showOnMap);
        if (true === $showOnMap) {
            return ['map-search'];
        }

        return ['text-search'];
    }

    private static function calculateBoundingBox($latitude, $longitude, $distance): array
    {
        $distance = (int) $distance;
        if (-1 === $distance) {
            $distance = 100;
        }

        $center = new GeoPoint($latitude, $longitude);
        $boundingBox = $center->boundingBox($distance, 'km');

        return [
            $boundingBox->getMinLatitude(),
            $boundingBox->getMinLongitude(),
            $boundingBox->getMaxLatitude(),
            $boundingBox->getMaxLongitude(),
        ];
    }

    private static function get($data, $index, $default)
    {
        return (isset($data[$index])) ? $data[$index] : $default;
    }
}
