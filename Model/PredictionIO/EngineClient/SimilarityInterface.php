<?php

namespace Richdynamix\PersonalisedProducts\Model\PredictionIO\EngineClient;

/**
 * Interface SimilarityInterface
 *
 * @category    Richdynamix
 * @package     PersonalisedProducts
 * @author 		Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
interface SimilarityInterface
{
    public function sendQuery(
        array $productIds,
        array $categores = [],
        array $whitelist = [],
        array $blacklist = []
    );
}
