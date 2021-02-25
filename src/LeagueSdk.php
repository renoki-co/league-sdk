<?php

namespace RenokiCo\LeagueSdk;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Str;

class LeagueSdk
{
    const BR1 = 'br1';
    const EUN1 = 'eun1';
    const EUW1 = 'euw1';
    const JP1 = 'jp1';
    const KR = 'kr';
    const LA1 = 'la1';
    const LA2 = 'la2';
    const NA1 = 'na1';
    const OC1 = 'oc1';
    const TR1 = 'tr1';
    const RU = 'ru';

    const AMERICAS = 'americas';
    const ASIA = 'asia';
    const EUROPE = 'europe';

    /**
     * Specify the cache store class for the caching.
     *
     * @var \Illuminate\Contracts\Cache\Store
     */
    public static $cacheStore;

    /**
     * Set the cache duration in seconds or
     * until a DateTime instance.
     *
     * @var \DateTime|int
     */
    public static $cacheDuration = 3600;

    /**
     * The token for API access.
     *
     * @var string
     */
    protected static $token;

    /**
     * Set the cache store class for caching.
     *
     * @param  string  $class
     * @return void
     */
    public static function setCacheStore(string $class)
    {
        static::$cacheStore = $class;
    }

    /**
     * Set the cache duration in seconds or
     * until a DateTime instance.
     *
     * @param  \DateTime|int  $seconds
     * @return void
     */
    public static function setCacheDuration(int $seconds)
    {
        static::$cacheDuration = $seconds;
    }

    /**
     * Set the token for API calls.
     *
     * @param  string  $token
     * @return void
     */
    public static function setToken(string $token)
    {
        static::$token = $token;
    }

    /**
     * Retrieve the JSON contents from an URL and parse it as array.
     *
     * @param  string  $url
     * @return array|null
     */
    protected static function readFromJson(string $url)
    {
        $callback = function () use ($url) {
            return json_decode(file_get_contents($url), true);
        };

        if (static::$cacheStore) {
            $key = Str::slug($url);

            if ($value = Cache::get($key)) {
                return $value;
            }

            $value = call_user_func($callback);

            Cache::put($key, $value, static::$cacheDuration);

            return $value;
        }

        return call_user_func($callback);
    }

    public static function client(
        string $region = null,
        string $game = null,
        string $resource = null,
        string $version = null
    ) {
        $region = $region ?: static::NA1;

        $options = [
            'base_uri' => "https://{$region}.api.riotgames.com/{$game}/{$resource}/{$version}",
            RequestOptions::HEADERS => [
                'X-Riot-Token' => static::$token,
            ],
            RequestOptions::VERIFY => true,
        ];

        return new Client($options);
    }
}
