<?php

namespace Richdynamix\PersonalisedProducts\Block\Product\ProductList;

use \Magento\Framework\View\Element\Template;
use \Magento\Catalog\Block\Product\Context as Context;
use \Magento\Checkout\Model\ResourceModel\Cart as Cart;
use \Magento\Catalog\Model\Product\Visibility as Visibility;
use \Magento\Checkout\Model\Session as Session;
use \Magento\Framework\Module\Manager as Manager;
use Richdynamix\PersonalisedProducts\Helper\Config as Config;

class Upsell extends \Magento\Catalog\Block\Product\ProductList\Upsell
{
    protected $_config;

    public function __construct(
        Context $context,
        Cart $checkoutCart,
        Visibility $catalogProductVisibility,
        Session $checkoutSession,
        Manager $moduleManager,
        array $data = [],
        Config $config
    ) {
        $this->_config = $config;
        parent::__construct(
            $context,
            $checkoutCart,
            $catalogProductVisibility,
            $checkoutSession,
            $moduleManager,
            $data
        );
    }

    protected function _prepareData()
    {

        if (!$this->_config->isEnabled()) {
            return parent::_prepareData();
        }

        $product = $this->_coreRegistry->registry('product');
        /* @var $product \Magento\Catalog\Model\Product */
        $this->_itemCollection = $product->getUpSellProductCollection()->setPositionOrder()->addStoreFilter();
        if ($this->moduleManager->isEnabled('Magento_Checkout')) {
            $this->_addProductAttributesAndPrices($this->_itemCollection);
        }
        $this->_itemCollection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());

        $this->_itemCollection->load();

        /**
         * Updating collection with desired items
         */
        $this->_eventManager->dispatch(
            'catalog_product_upsell',
            ['product' => $product, 'collection' => $this->_itemCollection, 'limit' => null]
        );

        foreach ($this->_itemCollection as $product) {
            $product->setDoNotUseCategoryId(true);
        }

        return $this;
    }

}