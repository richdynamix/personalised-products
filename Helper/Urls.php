<?php

namespace Richdynamix\PersonalisedProducts\Helper;

/**
 * Class Urls
 *
 * @category    Richdynamix
 * @package     PersonalisedProducts
 * @author 		Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class Urls
{
    /**
     * Construct the URL from the correct values stored in the config
     *
     * @param $url
     * @param $port
     * @return string
     */
    public function buildUrl($url, $port)
    {
        return trim($this->_checkUrlScheme($url), '/') . ":" . $port;
    }

    /**
     * Check the admin user has added a URL scheme (default to http://)
     *
     * @param $url
     * @return string
     */
    protected function _checkUrlScheme($url)
    {
        $parsed = parse_url($url);
        if (empty($parsed['scheme'])) {
            $url = 'http://' . $url;
        }

        return $url;
    }

}
