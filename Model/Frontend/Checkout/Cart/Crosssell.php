<?php

namespace Richdynamix\PersonalisedProducts\Model\Frontend\Checkout\Cart;

use \Richdynamix\PersonalisedProducts\Model\PredictionIO\EngineClient\Complementary;
use \Magento\Customer\Model\Session as CustomerSession;

class Crosssell
{
    protected $_complementaryEngine;

    protected $_customerSession;

    public function __construct(Complementary $complementaryEngine, CustomerSession $customerSession)
    {
        $this->_complementaryEngine = $complementaryEngine;
        $this->_customerSession = $customerSession;
    }

    public function getProductCollection($productIds)
    {
        $products = $this->_complementaryEngine->sendQuery($productIds);

        if ($products['rules']) {
            return $this->_getPredictedProducts($products['rules']);
        }

        return false;

    }

    protected function _getPredictedProducts($items)
    {
        $productIds = [];
        foreach ($items as $item) {
            $this->_getProductIds($item['itemScores'], $productIds);
        }

        return $productIds;
    }

    protected function _getProductIds($items, &$productIds)
    {
        foreach ($items as $item) {
            $productIds[] = $item['item'];
        }

        return $this;
    }

}