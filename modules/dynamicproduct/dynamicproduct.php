<?php

use classes\models\DynamicConfig;

class DynamicProductOverride extends DynamicProduct
{
    public function hookDisplayHeader()
    {
        
        $this->context->controller->addJqueryPlugin('fancybox');
        
        
        $this->media->addCSS(
           array(
              $this->media->getCSSDir().'global.css',
           )
        );
        
        $controller_name = Tools::getValue('controller');
        
        if ($controller_name === 'product') {
            $id_product = (int)Tools::getValue('id_product');
            $product_config = new DynamicConfig($id_product);
            $this->smarty->assign('dp_config', $product_config);
            if ((int)$product_config->active) {
                
                $this->media->addCSS(
                   array(
                      'views/css/front/vendor.css',
                      'views/css/front/common.css',
                      //'views/css/front/dp-product-buttons.css',
                   )
                );
                
   
                
                
                $this->media->addCSS(
                   array(
                      'views/css/front/vendor.css',
                      'views/css/front/common.css',
                      //'views/css/front/dp-cart-summary.css',
                   )
                );
                
                
            }
            
        }
    }
}