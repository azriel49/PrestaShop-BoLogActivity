<?php
/**
 * 2013-2014 Frédéric BENOIST
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 *
 *  @author    Frédéric BENOIST <http://www.fbenoist.com/>
 *  @copyright 2013-2014 Frédéric BENOIST
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

if (!defined('_PS_VERSION_'))
	exit;

class BoLogActivity extends Module
{
	public function __construct()
	{
		$this->name = 'bologactivity';
		$this->tab = 'administration';
		$this->version = '1.1';
		$this->author = 'Frédéric BENOIST';
		$this->need_instance = 0;
		$this->is_configurable = 1;
		$this->bootstrap = true;

		parent::__construct();
		$this->displayName = $this->l('BO Log Activity');
		$this->description = $this->l('Log module install/uninstall and carrier update');
	}

	public function install()
	{
		if (!parent::install()
			|| !$this->registerHook('actionCarrierUpdate')
			|| !$this->registerHook('actionModuleInstallAfter')
			|| !$this->registerHook('actionModuleRegisterHookAfter')
			|| !$this->registerHook('actionModuleUnRegisterHookAfter'))
			return false;
		return true;
	}

	public function uninstall()
	{
		if (!$this->unregisterHook('actionModuleInstallAfter')
			|| !$this->unregisterHook('actionModuleRegisterHookAfter')
			|| !$this->unregisterHook('actionModuleUnRegisterHookAfter')
			|| !parent::uninstall())
			return false;
		return true;
	}

    public function getContent(){
        $output = null;

        if (Tools::isSubmit('submit'.$this->name))
        {

            $bla_email=strval(Tools::getValue('BLA_EMAIL'));
            if(
                empty($bla_email)
                || !Validate::isGenericName($bla_email)
            ){
                $output .= $this->displayError($this->l('Invalid Email value'));
            }

            if(empty($output)){
                Configuration::updateValue('BLA_EMAIL', $bla_email);
                Configuration::updateValue('BLA_ACTIV_EMAIL', Tools::getValue('BLA_ACTIV_EMAIL'));
                Configuration::updateValue('BLA_ISLOG_CARUP', Tools::getValue('BLA_ISLOG_CARUP'));
                Configuration::updateValue('BLA_ISLOG_MODINST', Tools::getValue('BLA_ISLOG_MODINST'));
                Configuration::updateValue('BLA_ISLOG_MODREG', Tools::getValue('BLA_ISLOG_MODREG'));
                Configuration::updateValue('BLA_ISLOG_MODUREG', Tools::getValue('BLA_ISLOG_MODUREG'));
                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }

        }
        return $output.$this->displayForm();
    }

    public function displayForm()
    {
        // Get default language
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        // Init Fields form array
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Settings'),
            ),
            'input' => array(
                array(
                    'type' => 'switch',
                    'label' => $this->l('Activate e-mail notification'),
                    'name' => 'BLA_ACTIV_EMAIL',
                    'is_bool' => true,
                    'values' => array(
                        array( 'id' => 'BLA_ACTIV_EMAIL_ON', 'value' => 1, 'label' => $this->l('Yes')),
                        array( 'id' => 'BLA_ACTIV_EMAIL_OFF', 'value' => 0, 'label' => $this->l('No')),
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('E-mail for Notifications'),
                    'name' => 'BLA_EMAIL',
                    'size' => 20,
                    'required' => true
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'button'
            )
        );

        // Select what you want to log
        $fields_form[1]['form'] = array(
            'legend' => array(
                'title' => $this->l('Select what you want to log'),
            ),
            'input' => array(
                array(
                    'type' => 'switch',
                    'label' => $this->l('Log Carrier Update'),
                    'name' => 'BLA_ISLOG_CARUP',
                    'is_bool' => true,
                    'values' => array(
                        array( 'id' => 'BLA_ISLOG_CARUP_ON', 'value' => 1, 'label' => $this->l('Yes')),
                        array( 'id' => 'BLA_ISLOG_CARUP_OFF', 'value' => 0, 'label' => $this->l('No')),
                    )
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Log Module Install'),
                    'name' => 'BLA_ISLOG_MODINST',
                    'is_bool' => true,
                    'values' => array(
                        array( 'id' => 'BLA_ISLOG_MODINST_ON', 'value' => 1, 'label' => $this->l('Yes')),
                        array( 'id' => 'BLA_ISLOG_MODINST_OFF', 'value' => 0, 'label' => $this->l('No')),
                    )
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Log Module Register Hook'),
                    'name' => 'BLA_ISLOG_MODREG',
                    'is_bool' => true,
                    'values' => array(
                        array( 'id' => 'BLA_ISLOG_MODREG_ON', 'value' => 1, 'label' => $this->l('Yes')),
                        array( 'id' => 'BLA_ISLOG_MODREG_OFF', 'value' => 0, 'label' => $this->l('No')),
                    )
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Log Module UnRegister Hook'),
                    'name' => 'BLA_ISLOG_MODUREG',
                    'is_bool' => true,
                    'values' => array(
                        array( 'id' => 'BLA_ISLOG_MODUREG_ON', 'value' => 1, 'label' => $this->l('Yes')),
                        array( 'id' => 'BLA_ISLOG_MODUREG_OFF', 'value' => 0, 'label' => $this->l('No')),
                    )
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'button'
            )
        );

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit'.$this->name;
        $helper->toolbar_btn = array(
            'save' =>
                array(
                    'desc' => $this->l('Save'),
                    'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                        '&token='.Tools::getAdminTokenLite('AdminModules'),
                ),
            'back' => array(
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );

        // Load current value
        $helper->fields_value['BLA_ISLOG_CARUP'] = Configuration::get('BLA_ISLOG_CARUP');
        $helper->fields_value['BLA_ISLOG_MODINST'] = Configuration::get('BLA_ISLOG_MODINST');
        $helper->fields_value['BLA_ISLOG_MODREG'] = Configuration::get('BLA_ISLOG_MODREG');
        $helper->fields_value['BLA_ISLOG_MODUREG'] = Configuration::get('BLA_ISLOG_MODUREG');
        $helper->fields_value['BLA_ACTIV_EMAIL'] = Configuration::get('BLA_ACTIV_EMAIL');
        $helper->fields_value['BLA_EMAIL'] = Configuration::get('BLA_EMAIL');

        return $helper->generateForm($fields_form);
    }

	private static function logObjectEvent($event, $object)
	{
		if ((!Validate::isLoadedObject(Context::getContext()->employee)) || (!Validate::isLoadedObject($object)))
			return;

		if (get_class($object) == get_class())
			return;

		if ((!Context::getContext()->employee->isLoggedBack()))
			return;

		$log_message = sprintf('(%s) %s', get_class($object), $event );
		PrestaShopLogger::addLog($log_message, 1, null, get_class($object), (int)$object->id, true, (int)Context::getContext()->employee->id);

        $bla_activ_mail_notif=Configuration::get('BLA_ACTIV_EMAIL');
        if(!empty($bla_activ_mail_notif) && $bla_activ_mail_notif){
            // get notif email
            $bla_email_notif=Configuration::get('BLA_EMAIL');
            // send e-mail
            mail($bla_email_notif,'[BO Log Activity]['.Configuration::get('PS_SHOP_NAME').'] '.$log_message,$log_message);
        }

	}

	public function hookactionCarrierUpdate($params)
	{
        if(Configuration::get('BLA_ISLOG_CARUP')){
            self::logObjectEvent('Update parameters', $params['carrier']);
        }
	}

	public function hookactionModuleInstallAfter($params)
	{
        if(Configuration::get('BLA_ISLOG_MODINST')) {
            self::logObjectEvent('Install module', $params['object']);
        }
	}

	public function hookactionModuleRegisterHookAfter($params)
	{
		if (!Validate::isHookName($params['hook_name']))
			return;
        if(Configuration::get('BLA_ISLOG_MODREG')) {
            self::logObjectEvent('Register Hook ' . $params['hook_name'], $params['object']);
        }
	}

	public function hookactionModuleUnRegisterHookAfter($params)
	{
		if (!Validate::isHookName($params['hook_name']))
			return;
        if(Configuration::get('BLA_ISLOG_MODUREG')) {
            self::logObjectEvent('Unregister Hook ' . $params['hook_name'], $params['object']);
        }
	}
}