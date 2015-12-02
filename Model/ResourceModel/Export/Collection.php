<?php

namespace Richdynamix\PersonalisedProducts\Model\ResourceModel\Export;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            'Richdynamix\PersonalisedProducts\Model\Export',
            'Richdynamix\PersonalisedProducts\Model\ResourceModel\Export'
        );
    }

}