<?php

namespace Richdynamix\PersonalisedProducts\Api\Data;

/**
 * Interface EngineInterface
 *
 * @category Richdynamix
 * @package  PersonalisedProducts
 * @author   Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
interface EngineInterface
{
    /**
     * @param array $productIds
     * @param array $categores
     * @param array $whitelist
     * @param array $blacklist
     * @return mixed
     */
    public function sendQuery(
        array $productIds,
        array $categores = [],
        array $whitelist = [],
        array $blacklist = []
    );
}
