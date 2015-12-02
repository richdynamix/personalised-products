<?php

namespace Richdynamix\PersonalisedProducts\Api\Data;

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
