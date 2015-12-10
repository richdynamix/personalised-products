<?php

namespace Richdynamix\PersonalisedProducts\Api\Data;

/**
 * Interface EventClientInterface
 *
 * @category  Richdynamix
 * @package   PersonalisedProducts
 * @author    Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
interface EventClientInterface
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

}
