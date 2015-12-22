<?php

namespace Richdynamix\PersonalisedProducts\Block\Product\ProductList;

use \Magento\TargetRule\Block\Catalog\Product\ProductList\Upsell;
use \Magento\Catalog\Block\Product\Context;
use \Magento\TargetRule\Model\ResourceModel\Index;
use \Magento\TargetRule\Helper\Data;
use \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use \Magento\Catalog\Model\Product\Visibility;
use \Magento\TargetRule\Model\IndexFactory;
use \Magento\Framework\Module\Manager as Manager;
use \Magento\Checkout\Model\Cart;
use \Richdynamix\PersonalisedProducts\Helper\Config;
use \Richdynamix\PersonalisedProducts\Model\Frontend\Catalog\Product\ProductList\Upsell as UpsellModel;

/**
 * Rewrite target rule product upsell block in enterprise edition
 * to switch out product collection for one returned from PredictionIO
 *
 * @category  Richdynamix
 * @package   PersonalisedProducts
 * @author    Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class TargetUpsell extends Upsell
{
    /**
     * @var Cart
     */
    protected $_cart;
    /**
     * @var Config
     */
    protected $_config;
    /**
     * @var UpsellModel
     */
    protected $_upsell;
    /**
     * @var Manager
     */
    protected $_moduleManager;

    /**
     * @param Context $context
     * @param Index $index
     * @param Data $targetRuleData
     * @param CollectionFactory $productCollectionFactory
     * @param Visibility $visibility
     * @param IndexFactory $indexFactory
     * @param Cart $cart
     * @param Config $config
     * @param UpsellModel $upsell
     * @param Manager $moduleManager
     */
    public function __construct(
        Context $context,
        Index $index,
        Data $targetRuleData,
        CollectionFactory $productCollectionFactory,
        Visibility $visibility,
        IndexFactory $indexFactory,
        Cart $cart,
        Config $config,
        UpsellModel $upsell,
        Manager $moduleManager
    ) {
        $this->_cart = $cart;
        $this->_config = $config;
        $this->_upsell = $upsell;
        $this->_moduleManager = $moduleManager;
        parent::__construct(
            $context,
            $index,
            $targetRuleData,
            $productCollectionFactory,
            $visibility,
            $indexFactory,
            $cart
        );
    }

    /**
     * Rewrite parent getAllItems method to use PredictionIO results when available
     * Rewrites the target rules for Enterprise edition
     *
     * @return array
     */
    public function getAllItems()
    {
        if (!$this->_config->isEnabled() || !$this->_config->getItem(Config::SIMILARITY_REPLACE_RULES)) {
            return parent::getAllItems();
        }

        $product = $this->_coreRegistry->registry('product');
        $categoryIds = $this->_upsell->getCategoryIds($product);
        $personalisedIds = $this->_upsell->getProductCollection([$product->getId()], $categoryIds);

        if (!$personalisedIds) {
            return parent::getAllItems();
        }

        $collection = $this->_upsell->getPersonalisedProductCollection($personalisedIds);

        if ($this->_moduleManager->isEnabled('Magento_Checkout')) {
            $this->_addProductAttributesAndPrices($collection);
        }

        $items = [];
        foreach ($collection as $product) {
            $product->setDoNotUseCategoryId(true);
            $items[] = $product;
        }

        return $items;
    }
}
