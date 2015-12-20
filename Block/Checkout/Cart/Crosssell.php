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
use \Magento\Catalog\Model\Product\LinkFactory;
use \Magento\Quote\Model\Quote\Item\RelatedProducts;
use \Richdynamix\PersonalisedProducts\Model\Products;

/**
 * Class Crosssel
 *
 * @category  Richdynamix
 * @package   PersonalisedProducts
 * @author    Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class Crosssell extends \Magento\Checkout\Block\Cart\Crosssell
{
    /**
     * @var Config
     */
    private $_config;

    /**
     * @var CrosssellModel
     */
    private $_crosssell;

    /**
     * @var
     */
    private $_itemCollection;

    /**
     * @var Manager
     */
    private $_moduleManager;

    /**
     * @var Products
     */
    private $_products;

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
     * @param Products $products
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
        Products $products,
        array $data = []
    ) {
        $this->_config = $config;
        $this->_crosssell = $crosssell;
        $this->_productFactory = $productFactory;
        $this->_moduleManager = $moduleManager;
        $this->_products = $products;
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
        if (!$this->_config->isEnabled()) {
            return parent::getItems();
        }

        $ninProductIds = $this->_getCartProductIds();
        $personalisedIds = $this->_crosssell->getProductCollection($ninProductIds);

        if (!$personalisedIds) {
            return parent::getItems();
        }

        $collection = $this->_products->getPersonalisedProductCollection($personalisedIds);

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
