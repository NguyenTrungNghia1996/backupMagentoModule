<?php

namespace Import\ImportCSV\Setup;

use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    private $eavSetupFactory;
    private $attributeSetFactory;
    private $attributeSet;
    private $categorySetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory, AttributeSetFactory $attributeSetFactory, CategorySetupFactory $categorySetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->categorySetupFactory = $categorySetupFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $installer = $setup;

            $installer->startSetup();
            // CREATE ATTRIBUTE SET
//        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
//
//        $attributeSet = $this->attributeSetFactory->create();
//        $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
//        $attributeSetId = $categorySetup->getDefaultAttributeSetId($entityTypeId);
//        $data = [
//            'attribute_set_name' => 'Thực Phẩm',
//            'entity_type_id' => $entityTypeId,
//            'sort_order' => 200,
//        ];
//        $attributeSet->setData($data);
//        $attributeSet->validate();
//        $attributeSet->save();
//        $attributeSet->initFromSkeleton($attributeSetId);
//        $attributeSet->save();

            // CREATE PRODUCT ATTRIBUTE
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'can_nang',
                [   'group'=>'Product Details',
                    'type' => 'int',
                    'label' => 'Cân Nặng',
                    'input' => 'select',
                    'backend' => '',
                    'wysiwyg_enabled'   => false,
                    'source' => '',
                    'required' => false,
                    'sort_order' => 10,
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                    'used_in_product_listing' => true,
                    'visible_on_front' => true,
                    'attribute_set_id' => 'Default',
                    'user_defined' => true,
                ]
            );
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'vi',
                [
                    'group'=>'Product Details',
                    'type' => 'int',
                    'label' => 'Vị',
                    'input' => 'select',
                    'backend' => '',
                    'wysiwyg_enabled'   => false,
                    'source' => '',
                    'required' => false,
                    'sort_order' => 11,
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                    'used_in_product_listing' => true,
                    'visible_on_front' => true,
                    'attribute_set_id' => 'Default',
                    'user_defined' => true,
                ]
            );
            $installer->endSetup();
        }
    }
}
