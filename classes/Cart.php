<?php
/**
* 2010-2019 Tuni-Soft
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* It is available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize the module for your
* needs please refer to
* http://doc.prestashop.com/display/PS15/Overriding+default+behaviors
* for more information.
*
* @author    Tuni-Soft
* @copyright 2010-2019 Tuni-Soft
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/
class Cart extends CartCore
{
    /*
    * module: dynamicproduct
    * date: 2019-07-07 15:28:44
    * version: 2.3.4
    */
    public function duplicate()
    {
        $id_cart_old = (int)$this->id;
        $result = parent::duplicate();
        $id_cart_new = (int)$this->id;
        if (Module::isEnabled('dynamicproduct')) {
            
            $module = Module::getInstanceByName('dynamicproduct');
            $module->hookCartDuplicated(array(
                'id_cart_old' => $id_cart_old,
                'id_cart_new' => $id_cart_new,
            ));
        }
        return $result;
    }
}
