<?php

namespace Richdynamix\PersonalisedProducts\Api\Data;


interface ExportInterface
{
    const INCREMENT_ID      = 'increment_id';
    const PRODUCT_ID        = 'product_id';
    const CREATION_TIME     = 'creation_time';
    const UPDATE_TIME       = 'update_time';
    const IS_EXPORTED       = 'is_exported';

    public function getId();

    public function getProductId();

    public function getCreationTime();

    public function getUpdateTime();

    public function isExported();

    public function setId($id);

    public function setProductId($productId);

    public function setCreationTime($creationTime);

    public function setUpdateTime($updateTime);

    public function setIsExported($isExported);
}