<?php

namespace Richdynamix\PersonalisedProducts\Model\Frontend\Checkout\Cart;

use Magento\Catalog\Model\Product\Visibility;
use \Magento\Catalog\Model\ProductFactory as ProductFactory;
use \Richdynamix\PersonalisedProducts\Model\PredictionIO\EngineClient\Complementary;
use \Magento\Customer\Model\Session as CustomerSession;
use \Magento\Checkout\Model\Session;

/**
 * Class Crosssell
 *
 * @category  Richdynamix
 * @package   PersonalisedProducts
 * @author    Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class Crosssell
{
    /**
     * @var Complementary
     */
    private $_complementaryEngine;

    /**
     * @var CustomerSession
     */
    private $_customerSession;

    /**
     * @var array
     */
    private $_basketProducts;

    /**
     * @var ProductFactory
     */
    private $_productFactory;

    /**
     * @var array
     */
    protected $_products;

    /**
     * @var Session
     */
    protected $_checkoutSession;

    /**
     * Crosssell constructor.
     * @param Complementary $complementaryEngine
     * @param CustomerSession $customerSession
     * @param ProductFactory $productFactory
     * @param Session $session
     */
    public function __construct(
        Complementary $complementaryEngine,
        CustomerSession $customerSession,
        ProductFactory $productFactory,
        Session $session
    ) {
        $this->_complementaryEngine = $complementaryEngine;
        $this->_customerSession = $customerSession;
        $this->_productFactory = $productFactory;
        $this->_checkoutSession = $session;
    }

    /**
     * Query the PredictionIO engine for product data
     *
     * @return array|bool
     */
    public function getProductCollection()
    {
        $this->_basketProducts = $this->_getCartProductIds();
        $products = $this->_complementaryEngine->sendQuery($this->_basketProducts);

        if ($products['rules']) {
            return $this->_getPredictedProducts($products['rules']);
        }

        return false;
    }

    /**
     * Loop over each of the rules in the returned data from PredictionIO
     *
     * @param array $items
     * @return array
     */
    private function _getPredictedProducts(array $items)
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
     * @param array $items
     * @param $productIds
     * @return $this
     */
    private function _getProductIds(array $items, &$productIds)
    {
        foreach ($items as $item) {
            if (!in_array($item['item'], $this->_basketProducts)) {
                $productIds[] = $item['item'];
            }
        }

        return $this;
    }

    /**
     * Get a new product collection from prediction IO result set
     *
     * @param array $personalisedIds
     * @return $this
     */
    public function getPersonalisedProductCollection(array $personalisedIds)
    {
        $collection = $this->_productFactory->create()->getCollection()
            ->addAttributeToFilter('entity_id', ['in', $personalisedIds])
            ->addAttributeToFilter('visibility', Visibility::VISIBILITY_BOTH)
            ->addAttributeToFilter('status', array('eq' => 1));
        return $collection;
    }

    /**
     * Get all product ids in the cart
     *
     * @return array
     */
    private function _getCartProductIds()
    {
        if ($this->_products === null) {
            $this->_products = [];
            foreach ($this->getQuote()->getAllItems() as $quoteItem) {
                /* @var $quoteItem \Magento\Quote\Model\Quote\Item */
                $product = $quoteItem->getProduct();
                $this->_products[] = $product->getEntityId();
            }
        }

        return $this->_products;
    }

    /**
     * Get the quote from the session
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->_checkoutSession->getQuote();
    }
}
