<?php

namespace Richdynamix\PersonalisedProducts\Model\PredictionIO\EngineClient;

/**
 * Interface EngineInterface
 *
 * @category    Richdynamix
 * @package     PersonalisedProducts
 * @author 		Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
interface EngineInterface
{
    public function sendQuery(
        array $productIds,
        array $categores = [],
        array $whitelist = [],
        array $blacklist = []
    );
}
