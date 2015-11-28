<?php

namespace Richdynamix\PersonalisedProducts\Model\PredictionIO;

/**
 * Interface EventServerInterface
 *
 * @category    Richdynamix
 * @package     PersonalisedProducts
 * @author 		Steven Richardson (steven@richdynamix.com) @mage_gizmo
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface EventServerInterface
{

    /**
     * @param int $customerId
     * @return mixed
     */
    public function saveCustomerData($customerId);

    /**
     * @param int $productId
     * @param array $categoryIds
     * @return mixed
     */
    public function saveProductData($productId, array $categoryIds);

    /**
     * @param int $customerId
     * @param int $productId
     * @return mixed
     */
    public function saveCustomerViewProduct($customerId, $productId);

    /**
     * @param int $customerId
     * @param int $productId
     * @return mixed
     */
    public function saveCustomerBuyProduct($customerId, $productId);

    /**
     * @param array $productIds
     * @return mixed
     */
    public function setOutOfStockItems(array $productIds);

}
