<?php

namespace Richdynamix\PersonalisedProducts\Model\PredictionIO;

use \predictionio\EventClient;
use \predictionio\EngineClient;

/**
 * PredictionIO factory to return ready to use engine or event server objects
 *
 * @category Richdynamix
 * @package  PersonalisedProducts
 * @author   Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class Factory
{
    /**
     * Factory create method for getting Event or Engine object
     *
     * @param $model
     * @param $entityUrl
     * @param null $accessKey
     * @return null|EngineClient|EventClient
     */
    public function create($model, $entityUrl, $accessKey = null)
    {
        if ('event' == $model) {
            return new EventClient($accessKey, $entityUrl);
        } elseif ('engine' == $model) {
            return new EngineClient($entityUrl);
        }

        return null;
    }
}
