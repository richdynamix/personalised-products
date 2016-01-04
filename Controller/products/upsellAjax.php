<?php

namespace Richdynamix\PersonalisedProducts\Controller\Products;

use Magento\Review\Controller\Product as ProductController;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class UpsellAjax
 *
 * @category  Richdynamix
 * @package   PersonalisedProducts
 * @author    Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class UpsellAjax extends ProductController
{
    public function execute()
    {
        $productId = (int) $this->getRequest()->getParam('id');
        $product = $this->loadProduct($productId);
        if (!$product) {
            return false;
        }
        /** @var \Magento\Framework\View\Result\Layout $resultLayout */
        $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
        return $resultLayout;
    }
}