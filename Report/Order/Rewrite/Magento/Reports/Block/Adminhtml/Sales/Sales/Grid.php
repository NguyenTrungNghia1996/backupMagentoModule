<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Report\Order\Rewrite\Magento\Reports\Block\Adminhtml\Sales\Sales;

use Report\Order\Model\ResourceModel\Report\Order\Collection;

class Grid extends \Magento\Reports\Block\Adminhtml\Grid\AbstractGrid
{
    protected $_columnGroupBy = 'entyty_id';
    protected function _construct()
    {
        parent::_construct();
    }

    public function getResourceCollectionName()
    {
        return $this->getFilterData()->getData('report_type') === 'updated_at_order'
            ? \Magento\Sales\Model\ResourceModel\Report\Order\Updatedat\Collection::class
            : Collection::class;
    }

///*    protected function _prepareColumns()
//    {
//        $this->setStoreIds($this->_getStoreIds());
//        $currencyCode = $this->getCurrentCurrencyCode();
//        $rate = $this->getRate($currencyCode);
//        $this->addColumn(
//            'test',
//            [
//                'header' => __('Test'),
//                'type' => 'currency',
//                'index' => 'total_refunded_amount',
//                'sortable' => false,
//                'header_css_class' => 'col-refunded',
//                'column_css_class' => 'col-refunded'
//            ]
//        );
//        $this->removeColumn("total_tax_amount");
//        $this->addExportType('*/*/exportSalesCsv', __('CSV'));
//        $this->addExportType('*/*/exportSalesExcel', __('Excel XML'));
//
//        return parent::_prepareColumns();
//    }*/
    protected function _prepareColumns()
    {
        $this->addColumn(
            'sales_representative',
            [
                'header' => __('Sale'),
                'index' => 'sales_representative',
                'type' => 'text',
                'total' => 'sum',
                'sortable' => false,
                'header_css_class' => 'col-sales-items',
                'column_css_class' => 'col-sales-items'
            ]
        );
        $this->addColumn(
            'state',
            [
                'header' => __('State'),
                'index' => 'state',
                'type' => 'text',
                'total' => 'sum',
                'sortable' => false,
                'header_css_class' => 'col-sales-items',
                'column_css_class' => 'col-sales-items'
            ]
        );
        $this->addColumn(
            'increment_id',
            [
                'header' => __('Order#'),
                'index' => 'increment_id',
                'type' => 'number',
                'total' => 'sum',
                'sortable' => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );



        $this->addColumn(
            'creation_date',
            [
                'header' => __('Order Date'),
                'index' => 'creation_date',
                'type' => 'date',
                'total' => 'sum',
                'sortable' => false,
                'header_css_class' => 'col-sales-items',
                'column_css_class' => 'col-sales-items'
            ]
        );

        $this->addColumn(
            'customer_name',
            [
                'header' => __('Customer Name'),
                'index' => 'customer_name',
                'type' => 'text',
                'total' => 'sum',
                'sortable' => false,
                'header_css_class' => 'col-sales-items',
                'column_css_class' => 'col-sales-items'
            ]
        );

        $this->addColumn(
            'country_id',
            [
                'header' => __('Country'),
                'index' => 'country_id',
                'type' => 'text',
                'total' => 'sum',
                'sortable' => false,
                'header_css_class' => 'col-sales-items',
                'column_css_class' => 'col-sales-items'
            ]
        );

        $this->addColumn(
            'shipping_amount',
            [
                'header' => __('Shipping'),
                'index' => 'shipping_amount',
                'type' => 'currency',
                'total' => 'sum',
                'sortable' => false,
                'header_css_class' => 'col-sales-items',
                'column_css_class' => 'col-sales-items'
            ]
        );

        $this->addColumn(
            'sku',
            [
                'header' => __('Sku'),
                'index' => 'sku',
                'type' => 'text',
                'total' => 'sum',
                'sortable' => false,
                'header_css_class' => 'col-sales-items',
                'column_css_class' => 'col-sales-items'
            ]
        );

        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'index' => 'name',
                'type' => 'text',
                'total' => 'sum',
                'sortable' => false,
                'header_css_class' => 'col-sales-items',
                'column_css_class' => 'col-sales-items'
            ]
        );

        $this->addColumn(
            'total_qty_ordered',
            [
                'header' => __('Qty. Ordered'),
                'index' => 'qty_ordered',
                'type' => 'number',
                'total' => 'sum',
                'sortable' => false,
                'header_css_class' => 'col-sales-items',
                'column_css_class' => 'col-sales-items'
            ]
        );

        $this->addColumn(
            'subtotal',
            [
                'header' => __('Price'),
                'index' => 'subtotal',
                'type' => 'currency',
                'total' => 'sum',
                'sortable' => false,
                'header_css_class' => 'col-sales-items',
                'column_css_class' => 'col-sales-items'
            ]
        );

        $this->addColumn(
            'grand_total',
            [
                'header' => __('Original Price'),
                'index' => 'grand_total',
                'type' => 'currency',
                'total' => 'sum',
                'sortable' => false,
                'header_css_class' => 'col-sales-items',
                'column_css_class' => 'col-sales-items'
            ]
        );


        $this->addExportType('*/*/exportSalesCsv', __('CSV'));
        $this->addExportType('*/*/exportSalesExcel', __('Excel XML'));

        return parent::_prepareColumns();
    }
}
