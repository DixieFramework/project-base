<?php

declare(strict_types=1);

namespace Groshy\Service;

use GuzzleHttp\Client;

class FacebookPictureFinder
{
    protected $facebookUserAlias;
    protected $response;

    /**
     * @param string $facebookUserAlias
     */
    public function __construct($facebookUserAlias)
    {
        $this->facebookUserAlias = $facebookUserAlias;
    }

    /**
     * @return string Facebook profile image URL or FALSE
     */
    public function getPictureUrl()
    {
        $client = new Client();
        $this->response = $client->get('http://graph.facebook.com/'.$this->facebookUserAlias.'/picture?redirect=false&width=200&height=200');
        $json = $this->response->json();
        return $json['data']['url'];
    }
}
