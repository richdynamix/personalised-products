<?php

namespace Richdynamix\PersonalisedProducts\Model\ResourceModel\Export;

/**
 * Class Collection
 *
 * @category    Richdynamix
 * @package     PersonalisedProducts
 * @author 		Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Collection constructor
     */
    protected function _construct()
    {
        $this->_init(
            'Richdynamix\PersonalisedProducts\Model\Export',
            'Richdynamix\PersonalisedProducts\Model\ResourceModel\Export'
        );
    }
}
