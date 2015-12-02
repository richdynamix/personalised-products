<?php

namespace Richdynamix\PersonalisedProducts\Model\Frontend\Checkout\Cart;

use \Richdynamix\PersonalisedProducts\Model\PredictionIO\EngineClient\Complementary;
use \Magento\Customer\Model\Session as CustomerSession;

/**
 * Class Crosssell
 *
 * @category    Richdynamix
 * @package     PersonalisedProducts
 * @author 		Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class Crosssell
{
    /**
     * @var Complementary
     */
    protected $_complementaryEngine;

    /**
     * @var CustomerSession
     */
    protected $_customerSession;

    /**
     * @var
     */
    protected $_basketProducts;

    /**
     * Crosssell constructor.
     * @param Complementary $complementaryEngine
     * @param CustomerSession $customerSession
     */
    public function __construct(Complementary $complementaryEngine, CustomerSession $customerSession)
    {
        $this->_complementaryEngine = $complementaryEngine;
        $this->_customerSession = $customerSession;
    }

    /**
     * Query the PredictionIO engine for product data
     *
     * @param $productIds
     * @return array|bool
     */
    public function getProductCollection($productIds)
    {
        $this->_basketProducts = $productIds;
        $products = $this->_complementaryEngine->sendQuery($productIds);

        if ($products['rules']) {
            return $this->_getPredictedProducts($products['rules']);
        }

        return false;
    }

    /**
     * Loop over each of the rules in the returned data from PredictionIO
     *
     * @param $items
     * @return array
     */
    protected function _getPredictedProducts($items)
    {
        $productIds = [];
        foreach ($items as $item) {
            $this->_getProductIds($item['itemScores'], $productIds);
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
    protected function _getProductIds($items, &$productIds)
    {
        foreach ($items as $item) {
            if (!in_array($item['item'], $this->_basketProducts)) {
                $productIds[] = $item['item'];
            }
        }

        return $this;
    }
}
