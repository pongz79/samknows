<?php
/**
 * Created by PhpStorm.
 * User: tiago
 * Date: 08/03/17
 * Time: 22:25
 */

namespace App\Http\Helpers;


use GuzzleHttp\Client;

class Fetch
{
    /**
     * Fetches data from an URI.
     *
     * @param string $uri The URI endpoint.
     * @return \Illuminate\Support\Collection
     */
    public static function fetchData($uri)
    {
        $client = new Client();

        $response = $client->get($uri);

        return collect(json_decode($response->getBody()->getContents()));
    }
}