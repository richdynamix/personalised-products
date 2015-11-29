<?php

namespace Richdynamix\PersonalisedProducts\Model\PredictionIO;

use \predictionio\EventClient;
use \predictionio\EngineClient;

/**
 * PredictionIO factory to return ready to use engine or event server objects
 *
 * @category    Richdynamix
 * @package     PersonalisedProducts
 * @author 		Steven Richardson (steven@richdynamix.com) @mage_gizmo
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Factory
{
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
