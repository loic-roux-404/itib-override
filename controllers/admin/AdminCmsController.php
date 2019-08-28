<?php

class AdminCmsController extends AdminCmsControllerCore
{
    public function renderForm()
    {
        $this->display = 'edit';
        $this->toolbar_btn['save-and-preview'] = array(
           'href' => '#',
           'desc' => $this->l('Save and preview')
        );
        $this->initToolbar();
        if (!$this->loadObject(true))
            return;
        $categories = CMSCategory::getCategories($this->context->language->id, false);
        $html_categories = CMSCategory::recurseCMSCategory($categories, $categories[0][1], 1, $this->getFieldValue($this->object, 'id_cms_category'), 1);
        
        // Add code to get image url
        $image_url = '';
        $imgName = $this->getImageValue($this->object);
        if($imgName) {
            $image = _PS_IMG_DIR_ . 'cms/' . $imgName;
            $image_url = ImageManager::thumbnail($image, $this->table.'_'.(int)$this->object->id.'.'.$this->imageType, 350,
               $this->imageType, true, true);
        }
        
        $this->fields_form = array(
           'tinymce' => true,
           'legend' => array(
              'title' => $this->l('CMS Page'),
              'image' => '../img/admin/tab-categories.gif'
           ),
           'input' => array(
               // custom template
              array(
                 'type' => 'select_category',
                 'label' => $this->l('CMS Category'),
                 'name' => 'id_cms_category',
                 'options' => array(
                    'html' => $html_categories,
                 ),
              ),
              array(
                 'type' => 'text',
                 'label' => $this->l('Meta title:'),
                 'name' => 'meta_title',
                 'id' => 'name', // for copy2friendlyUrl compatibility
                 'lang' => true,
                 'required' => true,
                 'class' => 'copy2friendlyUrl',
                 'hint' => $this->l('Invalid characters:').' <>;=#{}',
                 'size' => 50
              ),
              array(
                 'type' => 'text',
                 'label' => $this->l('Meta description'),
                 'name' => 'meta_description',
                 'lang' => true,
                 'hint' => $this->l('Invalid characters:').' <>;=#{}',
                 'size' => 70
              ),
              array(
                 'type' => 'tags',
                 'label' => $this->l('Meta keywords'),
                 'name' => 'meta_keywords',
                 'lang' => true,
                 'hint' => $this->l('Invalid characters:').' <>;=#{}',
                 'size' => 70,
                 'desc' => $this->l('To add "tags" click in the field, write something, then press "Enter"')
              ),
              array(
                 'type' => 'text',
                 'label' => $this->l('Friendly URL'),
                 'name' => 'link_rewrite',
                 'required' => true,
                 'lang' => true,
                 'hint' => $this->l('Only letters and the minus (-) character are allowed')
              ),
              array(
                 'type' => 'textarea',
                 'label' => $this->l('Page content'),
                 'name' => 'content',
                 'autoload_rte' => true,
                 'lang' => true,
                 'rows' => 5,
                 'cols' => 40,
                 'hint' => $this->l('Invalid characters:').' <>;=#{}'
              ),
               /* Add an fileupload component to the form */
              array(
                 'type' => 'file',
                 'label' => $this->l('Page image'),
                 'name' => 'CMS_IMG',
                 'desc' => $this->l('Upload an image for this page'),
                 'lang' => true,
                 'display_image' => true,
                 'image' => $image_url ? $image_url : false,
              ),
              array(
                 'type' => 'radio',
                 'label' => $this->l('Displayed:'),
                 'name' => 'active',
                 'required' => false,
                 'class' => 't',
                 'is_bool' => true,
                 'values' => array(
                    array(
                       'id' => 'active_on',
                       'value' => 1,
                       'label' => $this->l('Enabled')
                    ),
                    array(
                       'id' => 'active_off',
                       'value' => 0,
                       'label' => $this->l('Disabled')
                    )
                 ),
              ),
           ),
           'submit' => array(
              'title' => $this->l('   Save   '),
              'class' => 'button'
           )
        );
        if (Shop::isFeatureActive())
        {
            $this->fields_form['input'][] = array(
               'type' => 'shop',
               'label' => $this->l('Shop association:'),
               'name' => 'checkBoxShopAsso',
            );
        }
        $this->tpl_form_vars = array(
           'active' => $this->object->active
        );
        return AdminControllerCore::renderForm();
    }
    
    public function postProcess()
    {
        $languages = Language::getLanguages(false);
        $update_images_values = false;
        
        foreach ($languages as $lang)
        {
            if (isset($_FILES['CMS_IMG'])
               && isset($_FILES['CMS_IMG']['tmp_name'])
               && !empty($_FILES['CMS_IMG']['tmp_name']))
            {
                if ($error = ImageManager::validateUpload($_FILES['CMS_IMG'], 4000000))
                    return $error;
                else
                {
                    $ext = substr($_FILES['CMS_IMG']['name'], strrpos($_FILES['CMS_IMG']['name'], '.') + 1);
                    $file_name = md5($_FILES['CMS_IMG']['name']).'.'.$ext;
                    
                    if (!move_uploaded_file($_FILES['CMS_IMG']['tmp_name'],
                       _PS_IMG_DIR_ .'cms'.DIRECTORY_SEPARATOR.$file_name))
                        return Tools::displayError($this->l('An error occurred while attempting to upload the file.'));
                    else
                    {
                        $values['CMS_IMG'][$lang['id_lang']] = $file_name;
                    }
                }
                
                $update_images_values = true;
                $cms = new CMS((int)Tools::getValue('id_cms'));
                $cms->CMS_IMG = $file_name;
                $cms->update();
            }
        }
        
        parent::postProcess();
    }
    
    public function getImageValue()
    {
        $db = Db::getInstance();
        $sql = 'SELECT CMS_IMG FROM '._DB_PREFIX_.'cms_lang WHERE id_cms = ' . $this->object->id;
        return $db->getValue($sql);
    }
}