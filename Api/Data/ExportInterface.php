<?php

namespace Richdynamix\PersonalisedProducts\Api\Data;


/**
 * Interface ExportInterface
 *
 * @category Richdynamix
 * @package  PersonalisedProducts
 * @author   Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
interface ExportInterface
{
    /**
     * Increment ID of the table
     */
    const INCREMENT_ID = 'increment_id';

    /**
     * Product ID to export
     */
    const PRODUCT_ID   = 'product_id';

    /**
     * Table row created time
     */
    const CREATION_TIME = 'creation_time';

    /**
     * Table row updated time
     */
    const UPDATE_TIME   = 'update_time';

    /**
     * Is the product exported
     */
    const IS_EXPORTED   = 'is_exported';

    /**
     * Get the table increment ID
     *
     * @return mixed
     */
    public function getId();

    /**
     * Get the product ID
     *
     * @return mixed
     */
    public function getProductId();

    /**
     * Get the created time
     *
     * @return mixed
     */
    public function getCreationTime();

    /**
     * Get the updated time
     *
     * @return mixed
     */
    public function getUpdateTime();

    /**
     * Check the product was exported
     *
     * @return mixed
     */
    public function isExported();

    /**
     * Set the table increment ID on a row
     *
     * @param $id
     * @return mixed
     */
    public function setId($incrementId);

    /**
     * Set the product ID on a row
     *
     * @param $productId
     * @return mixed
     */
    public function setProductId($productId);

    /**
     * Set created time on a row
     *
     * @param $creationTime
     * @return mixed
     */
    public function setCreationTime($creationTime);

    /**
     * Set updated time on a row
     *
     * @param $updateTime
     * @return mixed
     */
    public function setUpdateTime($updateTime);

    /**
     * Set the product is exported
     *
     * @param $isExported
     * @return mixed
     */
    public function setIsExported($isExported);
}