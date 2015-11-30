<?php

namespace Richdynamix\PersonalisedProducts\Model\PredictionIO;

/**
 * Interface EngineClientInterface
 *
 * @category    Richdynamix
 * @package     PersonalisedProducts
 * @author 		Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
interface EngineClientInterface
{
    public function sendQuery($data);
}
