<?php
/**
 * This software is provided "as is" without warranty of any kind.
 *
 * Visit my website (http://prestacraft.com) for future updates, new articles and other awesome modules.
 *
 * @author     PrestaCraft
 * @copyright  PrestaCraft
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class PcCookieConsent extends Module
{
    public function __construct()
    {
        $this->name = 'pccookieconsent';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'PrestaCraft';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Cookie consent');
        $this->description = $this->l('Displays window with information about cookies.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        $this->setDefaultValue('PC_CC_POSITION', 'banner_bottom');
        $this->setDefaultValue('PC_CC_LAYOUT', 'block');
        $this->setDefaultValue('PC_CC_COLOR_BANNER', '#262222');
        $this->setDefaultValue('PC_CC_COLOR_BANNER_TEXT', '#e0d4d4');
        $this->setDefaultValue('PC_CC_COLOR_BUTTON', '#c3cf3a');
        $this->setDefaultValue('PC_CC_COLOR_BUTTON_TEXT', '#0d0b0b');
        $this->setDefaultValue('PC_CC_LINK', 'default');
        $this->setDefaultValue('PC_CC_TEXT_MESSAGE', 'This website uses cookies to ensure you get the best experience on our website.', true);
        $this->setDefaultValue('PC_CC_TEXT_BUTTON', 'Got it!', true);
        $this->setDefaultValue('PC_CC_TEXT_LINK', 'Learn more', true);

        return parent::install() && $this->registerHook('header') && $this->registerHook('backOfficeHeader');
    }

    public function getContent()
    {
        if ((bool)Tools::isSubmit('submitPccookieconsentModule') == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitPccookieconsentModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        $data = file_get_contents('http://prestacraft.com/version_checker.php?module='.$this->name.'&version='.$this->version.'');

        $html = '<style> .mColorPicker { width: 100px !important; } </style>
                <div class="panel panel-version-checker" style="text-align: center;">
                 <h3 style="text-align: center;">'.$this->l('Version checker').'</h3>
        '.$data.'</div>';

        return $html.$helper->generateForm(array($this->getConfigForm()));
    }

    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }

        $languages = Language::getLanguages();

        foreach ($languages as $lang) {
            $langid = $lang['id_lang'];
            Configuration::updateValue(
                "PC_CC_TEXT_MESSAGE_{$langid}",
                Tools::getValue("PC_CC_TEXT_MESSAGE_{$langid}")
            );
            Configuration::updateValue(
                "PC_CC_TEXT_BUTTON_{$langid}",
                Tools::getValue("PC_CC_TEXT_BUTTON_{$langid}")
            );
            Configuration::updateValue(
                "PC_CC_TEXT_LINK_{$langid}",
                Tools::getValue("PC_CC_TEXT_LINK_{$langid}")
            );
        }
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        $languages = Language::getLanguages();
        $values = array();

        // Lang values
        foreach ($languages as $lang) {
            $values['PC_CC_TEXT_MESSAGE_'.$lang['id_lang']] =
                Configuration::get('PC_CC_TEXT_MESSAGE_'.$lang['id_lang']);
            $values['PC_CC_TEXT_BUTTON_'.$lang['id_lang']] =
                Configuration::get('PC_CC_TEXT_BUTTON_'.$lang['id_lang']);
            $values['PC_CC_TEXT_LINK_'.$lang['id_lang']] =
                Configuration::get('PC_CC_TEXT_LINK_'.$lang['id_lang']);
        }

        // Global values
        $values2 = array(
            'firstline' => 'by prestacraft',
            'secondline' => 'by prestacraft',
            'thirdline' => 'by prestacraft',
            'fourthline' => 'by prestacraft',
            'fifthline' => 'by prestacraft',
            'PC_CC_POSITION' => Configuration::get('PC_CC_POSITION'),
            'PC_CC_LAYOUT' => Configuration::get('PC_CC_LAYOUT'),
            'PC_CC_COLOR_BANNER' => Configuration::get('PC_CC_COLOR_BANNER'),
            'PC_CC_COLOR_BANNER_TEXT' => Configuration::get('PC_CC_COLOR_BANNER_TEXT'),
            'PC_CC_COLOR_BUTTON' => Configuration::get('PC_CC_COLOR_BUTTON'),
            'PC_CC_COLOR_BUTTON_TEXT' => Configuration::get('PC_CC_COLOR_BUTTON_TEXT'),
            'PC_CC_LINK' => Configuration::get('PC_CC_LINK'),
            'PC_CC_TEXT_LINK' => Configuration::get('PC_CC_TEXT_LINK'),
            'PC_CC_TEXT_BUTTON' => Configuration::get('PC_CC_TEXT_BUTTON'),
            'PC_CC_TEXT_MESSAGE' => Configuration::get('PC_CC_TEXT_MESSAGE'),
            'PC_CC_LINK_CUSTOM' => Configuration::get('PC_CC_LINK_CUSTOM'),
        );

        return array_merge($values, $values2);
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        $firstOption = array(
            array(
                'id_cms' => 0,
                'meta_title' => '---'
            )
        );

        $cmsOptions = CMS::listCms();

        $cms = array_merge($firstOption, $cmsOptions);

        $base = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => '<i class="icon-wrench" style="color: #2eacce;"></i><span style="font-size: 17px;
                                    color: #2eacce;font-weight:bold;margin-top:30px;display:inline-block;">
                                    &nbsp;&nbsp;'.$this->l("Position").'</span>',
                        'name' => 'firstline',
                    ),
                    array(
                        'type'      => 'radio',
                        'label'     => $this->l('Position'),
                        'name'      => 'PC_CC_POSITION',
                        'required'  => true,
                        'values'    => array(
                            array(
                                'id'    => 'banner_bottom',
                                'value' => 'banner_bottom',
                                'label' => $this->l('Banner bottom')
                            ),
                            array(
                                'id'    => 'banner_top',
                                'value' => 'banner_top',
                                'label' => $this->l('Banner top')
                            ),
                            array(
                                'id'    => 'banner_top_pushdown',
                                'value' => 'banner_top_pushdown',
                                'label' => $this->l('Banner top (pushdown)')
                            ),
                            array(
                                'id'    => 'floating_left',
                                'value' => 'floating_left',
                                'label' => $this->l('Floating left')
                            ),
                            array(
                                'id'    => 'floating_right',
                                'value' => 'floating_right',
                                'label' => $this->l('Floating right')
                            ),
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => '<i class="icon-desktop" style="color: #2eacce;"></i><span style="font-size: 17px;
                                    color: #2eacce;font-weight:bold;margin-top:30px;display:inline-block;">
                                    &nbsp;&nbsp;'.$this->l("Layout").'</span>',
                        'name' => 'secondline',
                    ),
                    array(
                        'type'      => 'radio',
                        'label'     => $this->l('Layout'),
                        'name'      => 'PC_CC_LAYOUT',
                        'required'  => true,
                        'values'    => array(
                            array(
                                'id'    => 'block',
                                'value' => 'block',
                                'label' => $this->l('Block')
                            ),
                            array(
                                'id'    => 'classic',
                                'value' => 'classic',
                                'label' => $this->l('Classic')
                            ),
                            array(
                                'id'    => 'edgeless',
                                'value' => 'edgeless',
                                'label' => $this->l('Edgeless')
                            ),
                            array(
                                'id'    => 'wire',
                                'value' => 'wire',
                                'label' => $this->l('Wire')
                            ),
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => '<i class="icon-pencil" style="color: #2eacce;"></i><span style="font-size: 17px;
                                    color: #2eacce;font-weight:bold;margin-top:30px;display:inline-block;">
                                    &nbsp;&nbsp;'.$this->l("Pallette").'</span>',
                        'name' => 'thirdline',
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Banner'),
                        'name' => 'PC_CC_COLOR_BANNER',
                        'col' => '3'
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Banner text'),
                        'name' => 'PC_CC_COLOR_BANNER_TEXT',
                        'col' => '3'
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Button'),
                        'name' => 'PC_CC_COLOR_BUTTON',
                        'col' => '3'
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Button text'),
                        'name' => 'PC_CC_COLOR_BUTTON_TEXT',
                        'col' => '3'
                    ),
                    array(
                        'type' => 'text',
                        'label' => '<i class="icon-external-link" style="color: #2eacce;"></i><span style="font-size: 17px;
                                    color: #2eacce;font-weight:bold;margin-top:30px;display:inline-block;">
                                    &nbsp;&nbsp;'.$this->l("Learn more link").'</span>',
                        'name' => 'fourthline',
                    ),
                    array(
                        'type'      => 'radio',
                        'label'     => $this->l('Learn more link'),
                        'name'      => 'PC_CC_LINK',
                        'values'    => array(
                            array(
                                'id'    => 'default',
                                'value' => 'default',
                                'label' => $this->l('Link to'). ' cookiesandyou.com'
                            ),
                            array(
                                'id'    => 'custom',
                                'value' => 'custom',
                                'label' => $this->l('Your own CMS page')
                            ),
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Your cookie policy CMS page'),
                        'name' => 'PC_CC_LINK_CUSTOM',
                        'options' => array(
                            'query' => $cms,
                            'id' => 'id_cms',
                            'name' => 'meta_title',
                        )
                    ),
                    array(
                        'type' => 'text',
                        'label' => '<i class="icon-book" style="color: #2eacce;"></i><span style="font-size: 17px;
                                    color: #2eacce;font-weight:bold;margin-top:30px;display:inline-block;">
                                    &nbsp;&nbsp;'.$this->l("Content").'</span>',
                        'name' => 'fifthline',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );

        foreach (Language::getLanguages() as $lang) {
            $base['form']['input'][] = array(
                'type' => 'text',
                'label' => $this->l('Cookie consent message').'<br /><small>'.$this->l('for language'). ' <strong>'.$lang['name'].'</strong></small>',
                'name' => 'PC_CC_TEXT_MESSAGE_'.$lang['id_lang'],
            );

            $base['form']['input'][] = array(
                'type' => 'text',
                'label' => $this->l('Button text').'<br /><small>'.$this->l('for language'). ' <strong>'.$lang['name'].'</strong></small>',
                'name' => 'PC_CC_TEXT_BUTTON_'.$lang['id_lang'],
            );

            $base['form']['input'][] = array(
                'type' => 'text',
                'label' => $this->l('Learn more text').'<br /><small>'.$this->l('for language'). ' <strong>'.$lang['name'].'</strong></small>',
                'name' => 'PC_CC_TEXT_LINK_'.$lang['id_lang'],
            );
        }

        return $base;
    }

    private function setDefaultValue($name, $value, $lang = false)
    {
        $values = array();

        if (!Configuration::hasKey($name)) {
            if ($lang) {
                foreach (Language::getLanguages() as $language) {
                    $values[$language['id_lang']] = $value;
                }

                Configuration::updateValue($name, $values);
            } else {
                Configuration::updateValue($name, $value);
            }
        }
    }

    public function hookHeader()
    {
        if (PcCookieConsent::getVersion() != "1.6") {
            $this->context->controller->registerStylesheet(
                'modules-pccookieconsent-css',
                'modules/'.$this->name.'/views/css/cookieconsent.min.css'
            );

            $this->context->controller->registerJavascript(
                'modules-pccookieconsent-js',
                'modules/'.$this->name.'/views/js/cookieconsent.min.js'
            );
        } else {
            $this->context->controller->addJS(($this->_path).'/views/js/cookieconsent.min.js');
            $this->context->controller->addCSS(($this->_path).'/views/css/cookieconsent.min.css', 'all');
        }

        if ((int)Configuration::get("PC_CC_LINK_CUSTOM") > 0) {
            $linkObj = new Link();
            $linkCMS = $linkObj->getCMSLink((int)Configuration::get("PC_CC_LINK_CUSTOM"));
        } else {
            $linkCMS = '';
        }

        $this->context->smarty->assign(array(
            'position' => Configuration::get("PC_CC_POSITION"),
            'layout' => Configuration::get("PC_CC_LAYOUT"),
            'color_banner' => Configuration::get("PC_CC_COLOR_BANNER"),
            'color_banner_text' => Configuration::get("PC_CC_COLOR_BANNER_TEXT"),
            'color_button' => Configuration::get("PC_CC_COLOR_BUTTON"),
            'color_button_text' => Configuration::get("PC_CC_COLOR_BUTTON_TEXT"),
            'link_type' => Configuration::get("PC_CC_LINK"),
            'link_custom' => $linkCMS,
            'text_message' => Configuration::get("PC_CC_TEXT_MESSAGE_".Context::getContext()->language->id),
            'text_button' => Configuration::get("PC_CC_TEXT_BUTTON_".Context::getContext()->language->id),
            'text_link' => Configuration::get("PC_CC_TEXT_LINK_".Context::getContext()->language->id),
        ));

        return $this->display(__FILE__, 'views/templates/hook/hook.tpl');
    }

    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    public static function getVersion()
    {
        return Tools::substr(_PS_VERSION_, 0, 3);
    }
}