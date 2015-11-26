<?php

namespace Richdynamix\PersonalisedProducts\Model\PredictionIO;

use \predictionio\EventClient;
use \predictionio\EngineClient;

class Factory
{
    public function create($model, $url, $accessKey = null)
    {
        if ('event' == $model) {
            return new EventClient($accessKey, $url);
        } elseif ('engine' == $model) {
            return new EngineClient($url);
        }

        return null;
    }
}