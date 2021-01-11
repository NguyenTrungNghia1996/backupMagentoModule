<?php

namespace Import\ImportCSV\Plugin;

require_once __DIR__ . '/src/SimpleXLS.php';
require_once __DIR__ . '/src/SimpleXLSX.php';

use SimpleXLS;
use SimpleXLSX;

class ChangCSV
{
    protected $eavAttributeFactory;
    protected $attributeOptionManagement;
    public function __construct(
        \Magento\Eav\Model\Entity\AttributeFactory $eavAttributeFactory,
        \Magento\Eav\Api\AttributeOptionManagementInterface $attributeOptionManagement
    ) {
        $this->eavAttributeFactory = $eavAttributeFactory;
        $this->attributeOptionManagement = $attributeOptionManagement;
    }
    public function beforeExecute()
    {
        $csvData = $this->convertFileToArray($_FILES);
        $total = sizeof($csvData)-1;
        $data_CSV_array = [];
        $fSkuKey = "";
        $skuKey = "";
        $nameKey = "";
        $priceKey = "";
        $descriptionKey = "";
        $setKey="";
        $customNameKey = "";
        $costPrice ="";
        $attributeNameKey = [];
        $attributeNameValue = [];
        $categoriesKey = [];
        $qtyKey = '';
        $arrayConfigurableProduct =[];
        $arrayAttribute=[];
        $configurable='';
        $configurableLable='';
        $oldFSku='';
        foreach ($csvData as $keyCSV => $csv) {
            if ($keyCSV == 0) {
                $csvItem = [
                    'sku',
                    'name',
                    'price',
                    'product_type',
                    'attribute_set_code',
                    'additional_attributes',
                    'categories',
                    'visibility',
                    'description',
                    'url_key',
                    'product_websites',
                    'configurable_variations',
                    'configurable_variation_labels',
                    'qty'
                ];
                $data_CSV_array[] = $csvItem;

                $fSkuKey = $this->findKey($csv, "Sản phẩm cha");
                $skuKey = $this->findKey($csv, "Mã sản phẩm");
                $nameKey = $this->findKey($csv, "Tên sản phẩm");
                $priceKey = $this->findKey($csv, "Giá bán");
                $descriptionKey = $this->findKey($csv, "Mô tả");
                $qtyKey = $this->findKey($csv, "Số lượng");
                $setKey = $this->findKey($csv, "Set");
                $costPrice = $this->findKey($csv, "Giá nhập");
                $customNameKey = $this->findKey($csv, "Tên tùy chỉnh");
                foreach ($csv as $keyItem => $Item) {
                    if (strtolower($Item) == strtolower("Danh mục")) {
                        $categoriesKey[] = (int)$keyItem;
                    }
                }
                foreach ($csv as $keyItem => $Item) {
                    $s =  strpos($Item, '_', 0);
                    if ($s >0) {
                        $attributeNameKey[] =(int)$keyItem;
                        $attributeNameValue[] =(string)$Item;
                    }
                }
            } else {
                $categories = 'Sản Phẩm,';
                foreach ($categoriesKey as $Item) {
                    if ($csv[$Item] == "") {
                        $a = '';
                    } else {
                        $a = $csv[$Item] . ',';
                    }
                    $categories .= $a;
                }
                $additional_attributes ="";
                foreach ($attributeNameKey as $key => $Item) {
                    if ($csv[$Item]!=null) {
                        $a = str_replace('-', '_', $this->filter($attributeNameValue[$key])) . '=' . $csv[$Item] . ",";
                        $att = str_replace('-', '_', $this->filter($attributeNameValue[$key]));
                        $arrayAttribute[$att][] =$csv[$Item];
                    } else {
                        $a ="";
                    }
                    $additional_attributes .= $a;
                }
                $additional_attributes = substr($additional_attributes, 0, -1);
                $categories =substr($categories, 0, -1);
                if ($customNameKey != "") {
                    $nameProduct =$csv[$nameKey] . " " . $csv[$customNameKey];
                } else {
                    $t="";
                    foreach ($attributeNameKey as $key => $Item) {
                        if ($csv[$Item]!=null) {
                            $a=" " . $csv[$Item];
                        } else {
                            $a="";
                        }
                        $t .=$a;
                    }
                    $nameProduct = $csv[$nameKey] . $t;
                }
                $customNameKey= "";
                if ($csv[$fSkuKey]==$csv[$skuKey]||$csv[$fSkuKey]=="") {
                    $csvItem = [
                        $csv[$skuKey],//Sku
                        $nameProduct,//name
                        $csv[$priceKey],//prices
                        'simple',//product_type
                        $csv[$setKey] ?: "Default",//attribute_set_code
                        $additional_attributes . ",cost=" . $csv[$costPrice],//additional_attributes
                        $categories,//categories
                        'Catalog, Search',//Visible
                        $csv[$descriptionKey] ?: 'Giới Thiệu:',
                        $this->filter($nameProduct),//url_key
                        'base',//product_website
                        '',//configurable_variations
                        '',
                        $csv[$qtyKey],
                    ];
                } else {
                    if ($oldFSku == $csv[$fSkuKey]) {
                        $configurable .= 'sku=' . $csv[$skuKey] . ',' . $additional_attributes . '|';
                    } else {
                        if (array_key_exists($csv[$fSkuKey], $arrayConfigurableProduct)) {
                            $config = $arrayConfigurableProduct[$csv[$fSkuKey]]['configurable_variations'];
                            $configurable = $config . 'sku=' . $csv[$skuKey] . ',' . $additional_attributes . '|';
                        } else {
                            $configurable ='sku=' . $csv[$skuKey] . ',' . $additional_attributes . '|';
                        }
                        $oldFSku = $csv[$fSkuKey];
                    }
                    $TempConfigValue ="";
                    foreach ($attributeNameKey as $key =>$item) {
                        if ($csv[$item]!=null) {
                            //$a =str_replace('-', '_', $this->filter($csv[$item])) . '=' . $csv[$item] . ",";
                            $t = substr($attributeNameValue[$key], 0, -1);
                            $a = $t . "=" . str_replace('-', '_', $this->filter($t)) . ",";
                        } else {
                            $a ="";
                        }
                        $TempConfigValue .=$a;
                    }
                    if (strlen($configurableLable)<strlen($TempConfigValue)) {
                        $configurableLable =$TempConfigValue;
                    }

                    $arrayConfigurableProduct[$csv[$fSkuKey]]['sku']=$csv[$fSkuKey];
                    $arrayConfigurableProduct[$csv[$fSkuKey]]['name']=$csv[$nameKey];
                    $arrayConfigurableProduct[$csv[$fSkuKey]]['configurable_variations']= $configurable;
                    $arrayConfigurableProduct[$csv[$fSkuKey]]['configurable_variation_labels'] =$configurableLable;
                    $arrayConfigurableProduct[$csv[$fSkuKey]]['attribute_set_code'] =$csv[$setKey];
                    $arrayConfigurableProduct[$csv[$fSkuKey]]['categories'] =$csv[$setKey];
                    $csvItem = [
                        $csv[$skuKey],//Sku
                        $nameProduct,//name
                        $csv[$priceKey],//prices
                        'virtual',//product_type
                        $csv[$setKey] ?: "Default",//attribute_set_code
                        $additional_attributes . ",cost=" . $csv[$costPrice],//additional_attributes
                        $categories,//categories
                        'Not Visible Individually',//Visible
                        $csv[$descriptionKey] ?: 'Giới Thiệu:',
                        $this->filter($nameProduct),//url_key
                        'base',//product_website
                        '',//configurable_variations
                        '',
                        $csv[$qtyKey],
                    ];
                }
                $data_CSV_array[] = $csvItem;
            }
        }
        foreach ($arrayConfigurableProduct as $keyProduct =>$itemProduct) {
            $itemProduct;
            $csvItem = [
                        $itemProduct['sku'],//Sku
                        $itemProduct['name'],//name
                        '',//prices
                        'configurable',//product_type
                        $itemProduct['attribute_set_code'],//attribute_set_code
                        '',//additional_attributes
                        $itemProduct['categories'],//categories
                        'Catalog, Search',//Visible
                        '',//description
                        $this->filter($itemProduct['name']),//url_key
                        'base',//product_website
                        $this->removeEndString($itemProduct['configurable_variations']),//configurable_variations
                        $this->removeEndString($itemProduct['configurable_variation_labels']),
                        '',
                    ];
            $data_CSV_array[] = $csvItem;
        }
        $this->convertArrayToFile($_FILES, $data_CSV_array);
        foreach ($arrayAttribute as $keyAtt => $item) {
            $magentoAttribute = $this->eavAttributeFactory->create()->loadByCode('catalog_product', $keyAtt);
            $attributeCode = $magentoAttribute->getAttributeCode();
            $magentoAttributeOptions = $this->attributeOptionManagement->getItems(
                'catalog_product',
                $attributeCode
            );
            $existingMagentoAttributeOptions = [];
            $newOptions = [];
            $counter = 0;
            foreach ($magentoAttributeOptions as $option) {
                if (!$option->getValue()) {
                    continue;
                }
                if ($option->getLabel() instanceof \Magento\Framework\Phrase) {
                    $label = $option->getText();
                } else {
                    $label = $option->getLabel();
                }

                if ($label == '') {
                    continue;
                }

                $existingMagentoAttributeOptions[] = $label;
                $newOptions['value'][$option->getValue()] = [$label, $label];
                $counter++;
            }

            foreach ($item as $option) {
                if ($option == '') {
                    continue;
                }

                if (!in_array($option, $existingMagentoAttributeOptions)) {
                    $newOptions['value']['option_' . $counter] = [$option, $option];
                }

                $counter++;
            }

            if (count($newOptions)) {
                $magentoAttribute->setOption($newOptions)->save();
            }
        }
        return null;
    }
    public function convertTextToLatin($string)
    {
        $vietnamese = ["à", "á", "ạ", "ả", "ã", "â", "ầ", "ấ", "ậ", "ẩ", "ẫ", "ă", "ằ", "ắ", "ặ", "ẳ", "ẵ", "è", "é", "ẹ", "ẻ", "ẽ", "ê", "ề", "ế", "ệ", "ể", "ễ", "ì", "í", "ị", "ỉ", "ĩ", "ò", "ó", "ọ", "ỏ", "õ", "ô", "ồ", "ố", "ộ", "ổ", "ỗ", "ơ", "ờ", "ớ", "ợ", "ở", "ỡ", "ù", "ú", "ụ", "ủ", "ũ", "ư", "ừ", "ứ", "ự", "ử", "ữ", "ỳ", "ý", "ỵ", "ỷ", "ỹ", "đ", "À", "Á", "Ạ", "Ả", "Ã", "Â", "Ầ", "Ấ", "Ậ", "Ẩ", "Ẫ", "Ă", "Ằ", "Ắ", "Ặ", "Ẳ", "Ẵ", "È", "É", "Ẹ", "Ẻ", "Ẽ", "Ê", "Ề", "Ế", "Ệ", "Ể", "Ễ", "Ì", "Í", "Ị", "Ỉ", "Ĩ", "Ò", "Ó", "Ọ", "Ỏ", "Õ", "Ô", "Ồ", "Ố", "Ộ", "Ổ", "Ỗ", "Ơ", "Ờ", "Ớ", "Ợ", "Ở", "Ỡ", "Ù", "Ú", "Ụ", "Ủ", "Ũ", "Ư", "Ừ", "Ứ", "Ự", "Ử", "Ữ", "Ỳ", "Ý", "Ỵ", "Ỷ", "Ỹ", "Đ"];

        $latin = ["a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "e", "e", "e", "e", "e", "e", "e", "e", "e", "e", "e", "i", "i", "i", "i", "i", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "y", "y", "y", "y", "y", "d", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "I", "I", "I", "I", "I", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "Y", "Y", "Y", "Y", "Y", "D"];

        return str_replace($vietnamese, $latin, $string);
    }
    public function filter($string)
    {
        $string = $this->convertTextToLatin($string);
        $string = preg_replace('#[^0-9a-z]+#i', '-', $string);
        $string = strtolower($string);
        $string = trim($string, '-');
        return $string;
    }
    public function convertFileToArray($file_name)
    {
        $extension=explode('.', $file_name["import_file"]["name"]);
        $data = [];
        if (end($extension) == 'xls') {
            $extension=explode('.', $file_name["import_file"]["name"]);
            $new_name= explode('.', $file_name["import_file"]["tmp_name"]);
            $file = $new_name[0] . '.xls';
            rename($file_name["import_file"]["tmp_name"], $file);
            if ($xls = SimpleXLS::parse($file)) {
                $data = $xls->rows();
            }
            unlink($file);
        } elseif (end($extension) == 'xlsx') {
            $extension=explode('.', $file_name["import_file"]["name"]);
            $new_name= explode('.', $file_name["import_file"]["tmp_name"]);
            $file = $new_name[0] . '.xlsx';
            rename($file_name["import_file"]["tmp_name"], $file);
            if ($xlsx = SimpleXLSX::parse($file)) {
                $data = $xlsx->rows();
            }
            unlink($file);
        } else {
            $file = fopen($file_name["import_file"]["tmp_name"], 'r');
            while (($line = fgetcsv($file)) !== false) {
                $data[] = $line;
            }
            unlink($file_name["import_file"]["tmp_name"]);
        }
        return $data;
    }
    public function (&$file_name, $data)
    {
        $file_name["import_file"]["type"] = 'application/vnd.ms-excel';
        $a = explode('.', $file_name["import_file"]["name"]);
        $file_name["import_file"]["name"] = $a[1] . '.csv';
        $t =$file_name["import_file"]["tmp_name"];
        $file = fopen($t, "x+");
        fputs($file, "\xEF\xBB\xBF");
        foreach ($data as $row) {
            fputcsv($file, $row);
        }
        fclose($file);
    }
    public function findKey($array, $string)
    {
        foreach ($array as $keyItem => $Item) {
            if (strtolower($Item) == strtolower($string)) {
                return (int)$keyItem;
            }
        }
    }
    public function removeEndString($str)
    {
        return substr($str, 0, -1);
    }
}
