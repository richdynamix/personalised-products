<?php

namespace Richdynamix\PersonalisedProducts\Model\Frontend\Checkout\Cart;

use \Richdynamix\PersonalisedProducts\Model\PredictionIO\EngineClient\Complementary;
use \Magento\Customer\Model\Session as CustomerSession;

/**
 * Class Crosssell
 *
 * @category Richdynamix
 * @package  PersonalisedProducts
 * @author   Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class Crosssell
{
    /**
     * @var Complementary
     */
    private $complementaryEngine;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var
     */
    private $basketProducts;

    /**
     * Crosssell constructor.
     * @param Complementary $complementaryEngine
     * @param CustomerSession $customerSession
     */
    public function __construct(Complementary $complementaryEngine, CustomerSession $customerSession)
    {
        $this->complementaryEngine = $complementaryEngine;
        $this->customerSession = $customerSession;
    }

    /**
     * Query the PredictionIO engine for product data
     *
     * @param $productIds
     * @return array|bool
     */
    public function getProductCollection($productIds)
    {
        $this->basketProducts = $productIds;
        $products = $this->complementaryEngine->sendQuery($productIds);

        if ($products['rules']) {
            return $this->getPredictedProducts($products['rules']);
        }

        return false;
    }

    /**
     * Loop over each of the rules in the returned data from PredictionIO
     *
     * @param $items
     * @return array
     */
    private function getPredictedProducts($items)
    {
        $productIds = [];
        foreach ($items as $item) {
            $this->getProductIds($item['itemScores'], $productIds);
        }

        return $productIds;
    }

    /**
     * Build product ID collection array from PredictionIO engine data
     *
     * @param $items
     * @param $productIds
     * @return $this
     */
    private function getProductIds($items, &$productIds)
    {
        foreach ($items as $item) {
            if (!in_array($item['item'], $this->basketProducts)) {
                $productIds[] = $item['item'];
            }
        }

        return $this;
    }
}
