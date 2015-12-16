<?php

namespace Richdynamix\PersonalisedProducts\Block\Product;

use \Magento\Catalog\Block\Product\Context;

/**
 * Class View
 *
 * @category  Richdynamix
 * @package   PersonalisedProducts
 * @author    Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class View extends \Magento\Framework\View\Element\Template
{
    /**
     * Route frontname used in URL
     */
    const ROUTE = "personalised";

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * View constructor.
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->_coreRegistry = $context->getRegistry();
        parent::__construct($context, $data = []);
    }

    /**
     * Get current product instance
     *
     * @return mixed
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }

    /**
     * Get the current products ID for recording the product view
     *
     * @return mixed
     */
    public function getProductId()
    {
        return $this->getProduct()->getId();
    }

}
