<?php

use classes\models\DynamicConfig;
use classes\models\DynamicEquation;
use classes\models\DynamicProportion;

class DynamicProductOverride extends DynamicProduct
{
    public function hookDisplayHeader()
    {
        $output = '';
        
        $this->context->controller->addJqueryPlugin('fancybox');
        
        $this->media->addJS(array(
           'views/js/front/vendor.js',
           'views/js/front/common.js',
           'views/js/front/dp-cart-summary.js'
        ));
        
        $this->media->addCSS(array(
           $this->media->getCSSDir() . 'global.css',
        ));
        
        $controller_name = Tools::getValue('controller');
        
        if ($controller_name === 'product') {
            $id_product = (int)Tools::getValue('id_product');
            $product_config = new DynamicConfig($id_product);
            $this->smarty->assign('dp_config', $product_config);
            if ((int)$product_config->active) {
                $this->handler->addCustomField($id_product);
                
                $this->media->addCSS(array(
                   'views/css/front/vendor.css',
                   'views/css/front/common.css',
                   //'views/css/front/dp-product-buttons.css',
                ));
                
                $this->assignEditInput();
                
                $source = $this->name;
                Media::addJsDef(array(
                   'dp_message' => array(
                      'short'               => $this->l(
                         'The value of the field _label_ must be at least _min_ characters long',
                         $source
                      ),
                      'long'                => $this->l(
                         'The value of the field _label_ must be at most _max_ characters long',
                         $source
                      ),
                      'empty'               => $this->l(
                         'The _label_ field is required',
                         $source
                      ),
                      'min_max'               => $this->l(
                         'The _label_ field must be between _min_ and _max_',
                         $source
                      ),
                      'select'              => $this->l(
                         'Please select an option for the _label_ field',
                         $source
                      ),
                      'confirm'             => $this->l(
                         'Are you sure you want to delete this customization?',
                         $source
                      ),
                      'remove_image_upload' => $this->l(
                         'Are you sure you want to delete this image?',
                         $source
                      ),
                      'remove_file_upload'  => $this->l(
                         'Are you sure you want to delete this file?',
                         $source
                      ),
                      'save_error'          => $this->l(
                         'An error occurred while saving your customization, please try again',
                         $source
                      ),
                      'uploading'          => $this->l(
                         'Uploading...',
                         $source
                      ),
                      'complete'          => $this->l(
                         'Complete',
                         $source
                      ),
                   )
                ));
                
                Media::addJsDef(array(
                   'dp_id_product'         => $id_product,
                   'dp_id_attribute'       => (int)Tools::getValue('id_product_attribute'),
                   'dp_config'             => $product_config,
                   'dp_active'             => DynamicConfig::isActive($id_product),
                   'dp_upload'             => $this->getFolderUrl('upload/'),
                   'dp_proportions'        => DynamicProportion::getDataByProduct($id_product),
                   'dp_combinations_count' => count($this->provider->getProductCombinations($id_product)),
                   'dp_calculator'         => $this->context->link->getModuleLink($this->name, 'calculator'),
                   'dp_customization'      => $this->context->link->getModuleLink($this->name, 'customization'),
                   'dp_uploader'           => $this->context->link->getModuleLink($this->name, 'uploader'),
                ));
                
                $this->context->controller->addJqueryUI('ui.spinner');
                $this->context->controller->addJqueryUI('ui.slider');
                $this->context->controller->addJqueryUI('ui.datepicker');
                $this->context->controller->addJqueryUI('ui.progressbar');
                
                $this->media->addJS(array(
                   $this->media->getJSDir() . 'cldr.js',
                   $this->media->getJSDir() . 'tools.js'
                ));
                
                $user_js_def = DynamicEquation::getUserJsDefinitions($id_product);
                if (count($user_js_def)) {
                    Media::addJsDef($user_js_def);
                }
                
                $this->media->addJS(array(
                   'views/js/plugins/jquery.ui.touch-punch.min.js',
                   'views/js/plugins/qtip/jquery.qtip.js',
                   'views/js/front/vendor.js',
                   'views/js/front/common.js',
                   'views/js/front/dp-product-buttons.js',
                   $this->media->getJSDir() . 'dynamic/custom.js',
                   $this->media->getJSDir() . 'dynamic/custom' . $id_product . '.js',
                   $this->media->getThemeJSDir() . 'dynamic/custom' . $id_product . '.js'
                ));
                
                $this->smarty->assign(array(
                   'dp_uploader' => $this->context->link->getModuleLink($this->name, 'uploader'),
                ));
            }
        }
        
        Media::addJsDef(array(
           'dp_version'     => $this->version,
           'dp_id_cart'     => Tools::getValue('dp_cart', 0),
           'dp_id_customer' => Tools::getValue('dp_customer', 0),
           'dp_public_path' => $this->getFolderUrl('views/js/')
        ));
        
        $output .= $this->display(__FILE__, 'views/templates/hook/display-header.tpl');
        
        $this->media->addCSS(array(
           'views/css/front/vendor.css',
           'views/css/front/common.css',
           //'views/css/front/dp-cart-summary.css'
        ));
        
        $this->media->addCSS(array(
           $this->media->getCSSDir() . 'dynamic.css',
           $this->media->getThemeCSSDir() . 'dynamic.css',
        ));
        
        if ($controller_name === 'product') {
            $id_product = (int)Tools::getValue('id_product');
            $this->media->addCSS(array(
               $this->media->getCSSDir() . 'dynamic' . $id_product . '.css',
               $this->media->getThemeCSSDir() . 'dynamic' . $id_product . '.css',
            ));
        }
        
        return $output;
    }
}