<?php

namespace Richdynamix\PersonalisedProducts\Model\PredictionIO\Event;

use \predictionio\EventClient;
use \Richdynamix\PersonalisedProducts\Helper\Config as Config;
use \Richdynamix\PersonalisedProducts\Helper\Urls;

class Client
{
    public function __construct(Config $config, Urls $urls)
    {
        $eventServerUrl = $urls->sanatiseUrl(
            $config->getConfigItem(Config::UPSELL_TEMPLATE_SERVER_URL),
            $config->getConfigItem(Config::UPSELL_TEMPLATE_SERVER_PORT)
        );

        $client = new EventClient(
            $config->getConfigItem(Config::UPSELL_TEMPLATE_SERVER_ACCESS_KEY),
            $eventServerUrl
        );

        return $client;

    }
}