<?php

namespace Richdynamix\PersonalisedProducts\Helper;

class Urls
{
    public function buildUrl($url, $port)
    {
        return trim($this->_checkUrlScheme($url), '/') . ":" . $port;
    }

    protected function _checkUrlScheme($url)
    {
        $parsed = parse_url($url);
        if (empty($parsed['scheme'])) {
            $url = 'http://' . $url;
        }

        return $url;
    }

}
