<?php

namespace Richdynamix\PersonalisedProducts\Model\ResourceModel;


class Export extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected $_date;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->_date = $date;
    }

    protected function _construct()
    {
        $this->_init('rp_export_products', 'increment_id');
    }

    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {

        if ($object->isObjectNew() && !$object->hasCreationTime()) {
            $object->setCreationTime($this->_date->gmtDate());
        }

        $object->setUpdateTime($this->_date->gmtDate());

        return parent::_beforeSave($object);
    }

    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        $select->where(
            'is_exported = ?',
            1
        )->limit(
            1
        );

        return $select;
    }

    protected function _getLoadByProductIdSelect($productId, $isExported = null)
    {
        $select = $this->getConnection()->select()->from(
            ['rp' => $this->getMainTable()]
        )->where(
            'rp.product_id = ?',
            $productId
        );

        if (!is_null($isExported)) {
            $select->where('rp.is_exported = ?', $isExported);
        }

        return $select;
    }

}