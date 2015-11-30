<?php

namespace Richdynamix\PersonalisedProducts\Helper;

class Urls
{
    /**
     * Urls constructor.
     */
    public function __construct()
    {
    }

    public function buildUrl($url, $port)
    {
        // todo urls sanitser to ensure correct format
        return $url . ":" . $port;
    }

}
