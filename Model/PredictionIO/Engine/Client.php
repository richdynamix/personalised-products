<?php

namespace Richdynamix\PersonalisedProducts\Model\PredictionIO\Engine;

use \predictionio\EngineClient;
use \Richdynamix\PersonalisedProducts\Helper\Config as Config;
use \Richdynamix\PersonalisedProducts\Helper\Urls;

class Client
{
    public function __construct(Config $config, Urls $urls)
    {
        $eventServerUrl = $urls->sanatiseUrl(
            $config->getConfigItem(Config::UPSELL_TEMPLATE_ENGINE_URL),
            $config->getConfigItem(Config::UPSELL_TEMPLATE_ENGINE_PORT)
        );

        $client = new EngineClient($eventServerUrl);

        return $client;

    }
}