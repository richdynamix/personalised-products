<?php

namespace Richdynamix\PersonalisedProducts\Block\Checkout\Cart;

use \Magento\Catalog\Block\Product\Context as Context;
use \Richdynamix\PersonalisedProducts\Helper\Config as Config;
use \Richdynamix\PersonalisedProducts\Model\Frontend\Catalog\Product\ProductList\Crosssell as CrosssellModel;
use \Magento\Catalog\Model\ProductFactory as ProductFactory;
use Magento\CatalogInventory\Helper\Stock as StockHelper;
use \Magento\Framework\Module\Manager as Manager;

class Crosssell extends \Magento\Checkout\Block\Cart\Crosssell
{

    protected $_config;

    protected $_crosssell;

    protected $_itemCollection;

    protected $_moduleManager;

    public function __construct(
        Context $context,
        Config $config,
        CrosssellModel $crosssell,
        ProductFactory $productFactory,
        Manager $moduleManager,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Model\Product\LinkFactory $productLinkFactory,
        \Magento\Quote\Model\Quote\Item\RelatedProducts $itemRelationsList,
        StockHelper $stockHelper,
        array $data = []
    ) {
        $this->_config = $config;
        $this->_crosssell = $crosssell;
        $this->_productFactory = $productFactory;
        $this->_moduleManager = $moduleManager;
        parent::__construct(
            $context,
            $checkoutSession,
            $productVisibility,
            $productLinkFactory,
            $itemRelationsList,
            $stockHelper,
            $data
        );
    }

    /**
     * Get crosssell items
     *
     * @return array
     */
    public function getItems()
    {
        if (!$this->_config->isEnabled()) {
            return parent::getItems();
        }

        $ninProductIds = $this->_getCartProductIds();
        $personalisedIds = $this->_crosssell->getProductCollection($ninProductIds);

        if (!$personalisedIds) {
            return parent::getItems();
        }

        $collection = $this->_productFactory->create()->getCollection();
        $collection->addAttributeToFilter('entity_id', ['in', $personalisedIds]);

        $this->_itemCollection = $collection;

        if ($this->_moduleManager->isEnabled('Magento_Checkout')) {
            $this->_addProductAttributesAndPrices($this->_itemCollection);
        }

        $items = [];
        foreach ($collection as $item) {
            $items[] = $item;
        }

        return $items;
    }
}
