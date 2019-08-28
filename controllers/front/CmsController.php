<?php

class CmsController extends CmsControllerCore
{
    
    /**
     * Assign template vars related to page content
     * @see CmsControllerCore::initContent()
     */
    public function initContent()
    {
        if (!empty($this->cms->CMS_IMG)) {
            $this->context->smarty->assign(
               'cms_image',
               _PS_IMG_.'cms/'.$this->cms->CMS_IMG
            );
        }
        
        $this->getBlog();
        
        parent::initContent();
    }
    
    
    public function getBlog()
    {
        include_once(_PS_MODULE_DIR_.'cmsblog/cmsblog.php');
        $cmsblog = new CmsBlog();
        
        
        $this->context->smarty->assign('blog_id', $cmsblog->getBlogId());
    }
}