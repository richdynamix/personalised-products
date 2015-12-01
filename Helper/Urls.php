<?php

namespace Richdynamix\PersonalisedProducts\Helper;

class Urls
{
    public function buildUrl($url, $port)
    {
        return $this->_checkUrlScheme($url.'/') . ":" . $port;
    }

    protected function _checkUrlScheme($url)
    {
        $parsed = parse_url($url);
        if (empty($parsed['scheme'])) {
            $url = 'http://' . trim($url, '/');
        }

        return $url;
    }

}
