<?php

namespace PHPStudios\DeleteOrder\Block\Adminhtml\Sales\Order;

use Magento\Sales\Block\Adminhtml\Order\View as OrderView;

class View extends OrderView
{
    /**
     * Adding the Delete Order Button in Order Detail Page
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addButton(
            'delete_btn',
            [
                'label' => 'Delete Order',
                'class' => '',
                'onclick' => 'deleteConfirm(\'' .
                    __('Do you want to delete this order?') . '\', \'' .
                    $this->getDeleteOrderUrl() . '\')'
            ]
        );
    }

    /**
     * @return string
     */
    public function getDeleteOrderUrl()
    {
        return $this->getUrl('delete/order/', ['_current' => true]);
    }
}
