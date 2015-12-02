<?php

namespace Richdynamix\PersonalisedProducts\Block\Product\ProductList;

use \Magento\Framework\View\Element\Template;
use \Magento\Catalog\Block\Product\Context as Context;
use \Magento\Checkout\Model\ResourceModel\Cart as Cart;
use \Magento\Catalog\Model\Product\Visibility as Visibility;
use \Magento\Checkout\Model\Session as Session;
use \Magento\Framework\Module\Manager as Manager;
use Richdynamix\PersonalisedProducts\Helper\Config as Config;
use \Magento\Catalog\Model\ProductFactory as ProductFactory;
use \Magento\Customer\Model\Session as CustomerSession;
use \Richdynamix\PersonalisedProducts\Model\Frontend\Catalog\Product\ProductList\Upsell as PersonalisedUpsell;

/**
 * Rewrite product upsell block to switch out product collection
 * for one returned from PredictionIO
 *
 * @category    Richdynamix
 * @package     PersonalisedProducts
 * @author 		Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class Upsell extends \Magento\Catalog\Block\Product\ProductList\Upsell
{
    /**
     * @var Config
     */
    protected $_config;

    /**
     * @var ProductFactory
     */
    protected $_productFactory;

    /**
     * @var PersonalisedUpsell
     */
    protected $_upsell;

    /**
     * Upsell constructor.
     * @param Context $context
     * @param Cart $checkoutCart
     * @param Visibility $productVisibility
     * @param Session $checkoutSession
     * @param Manager $moduleManager
     * @param ProductFactory $productFactory
     * @param Config $config
     * @param PersonalisedUpsell $upsell
     * @param CustomerSession $customerSession
     * @param array $data
     */
    public function __construct(
        Context $context,
        Cart $checkoutCart,
        Visibility $productVisibility,
        Session $checkoutSession,
        Manager $moduleManager,
        ProductFactory $productFactory,
        Config $config,
        PersonalisedUpsell $upsell,
        CustomerSession $customerSession,
        array $data = []
    ) {
        $this->_config = $config;
        $this->_productFactory = $productFactory;
        $this->_upsell = $upsell;
        $this->_customerSession = $customerSession;
        parent::__construct(
            $context,
            $checkoutCart,
            $productVisibility,
            $checkoutSession,
            $moduleManager,
            $data
        );
    }

    /**
     * Rewrite parent _prepareData method to use PredictionIO results when available
     *
     * @return $this
     */
    protected function _prepareData()
    {

        if (!$this->_config->isEnabled()) {
            return parent::_prepareData();
        }

        $product = $this->_coreRegistry->registry('product');
        $categoryIds = $this->_getCategoryIds($product);
        $personalisedIds = $this->_upsell->getProductCollection([$product->getId()], $categoryIds);

        if (!$personalisedIds) {
            return parent::_prepareData();
        }

        $collection = $this->_productFactory->create()->getCollection();
        $collection->addAttributeToFilter('entity_id', ['in', $personalisedIds]);

        $this->_itemCollection = $collection;

        if ($this->moduleManager->isEnabled('Magento_Checkout')) {
            $this->_addProductAttributesAndPrices($this->_itemCollection);
        }

        $this->_eventManager->dispatch(
            'catalog_product_upsell',
            ['product' => $product, 'collection' => $this->_itemCollection, 'limit' => null]
        );

        foreach ($this->_itemCollection as $product) {
            $product->setDoNotUseCategoryId(true);
        }

        return $this;
    }

    /**
     * Get array of category ID's the product belongs to
     *
     * @param $product
     * @return array
     */
    protected function _getCategoryIds($product)
    {
        if (!$this->_config->getItem(Config::SIMILARITY_USE_CATEGORY_FILTER)) {
            return [];
        }

        return $product->getCategoryIds();

    }
}
