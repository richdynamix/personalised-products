<?php

namespace Richdynamix\PersonalisedProducts\Block\Checkout\Cart;

use \Magento\Catalog\Block\Product\Context as Context;
use \Richdynamix\PersonalisedProducts\Helper\Config as Config;
use \Richdynamix\PersonalisedProducts\Model\Frontend\Checkout\Cart\Crosssell as CrosssellModel;
use \Magento\Catalog\Model\ProductFactory as ProductFactory;
use \Magento\CatalogInventory\Helper\Stock as StockHelper;
use \Magento\Framework\Module\Manager as Manager;
use \Magento\Catalog\Model\Product\Visibility;
use \Magento\Checkout\Model\Session;
use \Magento\Checkout\Block\Cart\Crosssell as CartCrossell;
use \Magento\Catalog\Model\Product\LinkFactory;
use \Magento\Quote\Model\Quote\Item\RelatedProducts;

/**
 * Class Crosssel
 *
 * @category Richdynamix
 * @package  PersonalisedProducts
 * @author   Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class Crosssell extends CartCrossell
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var CrosssellModel
     */
    private $crosssell;

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * Crosssell constructor.
     * @param Context $context
     * @param Config $config
     * @param CrosssellModel $crosssell
     * @param ProductFactory $productFactory
     * @param Manager $moduleManager
     * @param Visibility $productVisibility
     * @param Session $checkoutSession
     * @param LinkFactory $productLinkFactory
     * @param RelatedProducts $itemRelationsList
     * @param StockHelper $stockHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $config,
        CrosssellModel $crosssell,
        ProductFactory $productFactory,
        Manager $moduleManager,
        Visibility $productVisibility,
        Session $checkoutSession,
        LinkFactory $productLinkFactory,
        RelatedProducts $itemRelationsList,
        StockHelper $stockHelper,
        array $data = []
    ) {
        $this->config = $config;
        $this->crosssell = $crosssell;
        $this->productFactory = $productFactory;
        $this->moduleManager = $moduleManager;
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
     * Get the crossell items for the basket page
     *
     * @return array
     */
    public function getItems()
    {
        if (!$this->config->isEnabled()) {
            return parent::getItems();
        }

        $ninProductIds = $this->_getCartProductIds();
        $personalisedIds = $this->crosssell->getProductCollection($ninProductIds);

        if (!$personalisedIds) {
            return parent::getItems();
        }

        $collection = $this->productFactory->create()->getCollection();
        $collection->addAttributeToFilter('entity_id', ['in', $personalisedIds]);

        if ($this->moduleManager->isEnabled('Magento_Checkout')) {
            $this->_addProductAttributesAndPrices($collection);
        }

        $items = [];
        foreach ($collection as $item) {
            $items[] = $item;
        }

        return $items;
    }
}
