<?php

namespace Richdynamix\PersonalisedProducts\Model\Frontend\Catalog\Product\ProductList;

use \Richdynamix\PersonalisedProducts\Model\PredictionIO\EngineClient\Similarity;
use \Magento\Customer\Model\Session as CustomerSession;

class Upsell
{
    protected $_similarityEngine;

    protected $_customerSession;

    public function __construct(Similarity $similarityEngine, CustomerSession $customerSession)
    {
        $this->_similarityEngine = $similarityEngine;
        $this->_customerSession = $customerSession;
    }

    public function getProductCollection($productIds)
    {
        $this->_checkIsGuestCustomer($productIds);

        $products = $this->_similarityEngine->sendQuery($productIds);

        if ($products['itemScores']) {
            return $this->_getProductIds($products['itemScores']);
        }
        
        return false;

    }

    protected function _getProductIds($items)
    {
        $productIds = [];
        foreach ($items as $item) {
            $productIds[] = $item['item'];
        }

        return $productIds;
    }

    protected function _checkIsGuestCustomer(&$productIds)
    {
        if (!$this->_customerSession->isLoggedIn()) {
            // todo get recently viewed products and list together
//            $productIds = [];
        }

    }

}