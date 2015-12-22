<?php

namespace Richdynamix\PersonalisedProducts\Block\Checkout\Cart;

use \Magento\Catalog\Block\Product\Context;
use \Magento\TargetRule\Model\ResourceModel\Index;
use \Magento\TargetRule\Helper\Data;
use \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use \Magento\Catalog\Model\Product\Visibility;
use \Magento\CatalogInventory\Helper\Stock;
use \Magento\Checkout\Model\Session;
use \Magento\Catalog\Model\Product\LinkFactory;
use \Magento\TargetRule\Model\IndexFactory;
use \Magento\Catalog\Model\ProductTypes\ConfigInterface;
use \Magento\Catalog\Api\ProductRepositoryInterface;
use \Richdynamix\PersonalisedProducts\Helper\Config;
use \Magento\TargetRule\Block\Checkout\Cart\Crosssell;
use \Richdynamix\PersonalisedProducts\Model\Frontend\Checkout\Cart\Crosssell as CrosssellModel;
use \Magento\Catalog\Model\ProductFactory as ProductFactory;
use \Magento\Framework\Module\Manager as Manager;

/**
 * Class TargetCrosssell
 *
 * @category  Richdynamix
 * @package   PersonalisedProducts
 * @author    Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class TargetCrosssell extends Crosssell
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
     * @var ProductFactory
     */
    private $_productFactory;
    /**
     * @var Manager
     */
    private $_moduleManager;

    /**
     * TargetCrosssell constructor.
     * @param Context $context
     * @param Index $index
     * @param Data $targetRuleData
     * @param CollectionFactory $productCollectionFactory
     * @param Visibility $visibility
     * @param Stock $stockHelper
     * @param Session $session
     * @param LinkFactory $productLinkFactory
     * @param IndexFactory $indexFactory
     * @param ConfigInterface $productTypeConfig
     * @param ProductRepositoryInterface $productRepository
     * @param Config $config
     * @param CrosssellModel $crosssell
     * @param ProductFactory $productFactory
     * @param Manager $moduleManager
     * @param array $data
     */
    public function __construct(
        Context $context,
        Index $index,
        Data $targetRuleData,
        CollectionFactory $productCollectionFactory,
        Visibility $visibility,
        Stock $stockHelper,
        Session $session,
        LinkFactory $productLinkFactory,
        IndexFactory $indexFactory,
        ConfigInterface $productTypeConfig,
        ProductRepositoryInterface $productRepository,
        Config $config,
        CrosssellModel $crosssell,
        ProductFactory $productFactory,
        Manager $moduleManager,
        array $data = []
    ) {
        $this->_config = $config;
        $this->_crosssell = $crosssell;
        $this->_productFactory = $productFactory;
        $this->_moduleManager = $moduleManager;
        parent::__construct(
            $context,
            $index,
            $targetRuleData,
            $productCollectionFactory,
            $visibility,
            $stockHelper,
            $session,
            $productLinkFactory,
            $indexFactory,
            $productTypeConfig,
            $productRepository,
            $data
        );
    }

    /**
     * Get the crossell items for the basket page
     * Rewrites the target rules for Enterprise edition
     *
     * @return array
     */
    public function getItemCollection()
    {
        if (!$this->_config->isEnabled() || !$this->_config->getItem(Config::COMPLEMENTARY_REPLACE_RULES)) {
            return parent::getItemCollection();
        }

        $personalisedIds = $this->_crosssell->getProductCollection();
        if (!$personalisedIds) {
            return parent::getItemCollection();
        }

        $collection = $this->_crosssell->getPersonalisedProductCollection($personalisedIds);

        if ($this->_moduleManager->isEnabled('Magento_Checkout')) {
            $this->_addProductAttributesAndPrices($collection);
        }

        $this->_items = [];
        foreach ($collection as $item) {
            $this->_items[] = $item;
        }

        return $this->_items;
    }
}
