<?php
/*
* 2015- PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author TienTV <tranvinhtienit@gmail.com>
*  @copyright  2015- TienTV
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class BlockCMFacebook extends Module
{
	public function __construct()
	{
		$this->name = 'blockcmfacebook';
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'TienTV';

		$this->bootstrap = true;
		parent::__construct();
		$this->displayName = $this->l('Facebook Comment block');
		$this->description = $this->l('Displays a facebook comment box in product.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}


	public function install()
	{
		return
                parent::install() &&
			    Configuration::updateValue('blockcmfacebook_rows', '5') &&
			    Configuration::updateValue('blockcmfacebook_width', '100%') &&
			    Configuration::updateValue('blockcmfacebook_theme', 'light') &&
			    Configuration::updateValue('blockcmfacebook_orderby', 'social') &&
			    $this->registerHook('displayProductTab') &&
			    $this->registerHook('displayHeader');
	}

	public function uninstall()
	{
		// Delete configuration
		return
                Configuration::deleteByName('blockcmfacebook_rows') &&
                Configuration::deleteByName('blockcmfacebook_width') &&
                Configuration::deleteByName('blockcmfacebook_theme') &&
                Configuration::deleteByName('blockcmfacebook_orderby') &&
                parent::uninstall();
	}

	public function getContent()
	{
		$html = '';
		// If we try to update the settings
		if (Tools::isSubmit('submitModule'))
		{
			Configuration::updateValue('blockcmfacebook_rows', Tools::getValue('blockcmfacebook_rows'));
			Configuration::updateValue('blockcmfacebook_width', Tools::getValue('blockcmfacebook_width'));
			Configuration::updateValue('blockcmfacebook_theme', Tools::getValue('blockcmfacebook_theme'));
			Configuration::updateValue('blockcmfacebook_orderby', Tools::getValue('blockcmfacebook_orderby'));
			$html .= $this->displayConfirmation($this->l('Configuration updated'));
			$this->_clearCache('blockcmfacebook.tpl');
			Tools::redirectAdmin('index.php?tab=AdminModules&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
		}

		$html .= $this->renderForm();
		$blockcmfacebook_rows = Configuration::get('blockcmfacebook_rows');
		$blockcmfacebook_width = Configuration::get('blockcmfacebook_width');
		$blockcmfacebook_theme = Configuration::get('blockcmfacebook_theme');
		$blockcmfacebook_orderby = Configuration::get('blockcmfacebook_orderby');
		$this->context->smarty->assign('blockcmfacebook_rows', $blockcmfacebook_rows);
		$this->context->smarty->assign('blockcmfacebook_width', $blockcmfacebook_width);
		$this->context->smarty->assign('blockcmfacebook_theme', $blockcmfacebook_theme);
		$this->context->smarty->assign('blockcmfacebook_orderby', $blockcmfacebook_orderby);
		$this->context->smarty->assign('facebook_js_url', $this->_path.'blockcmfacebook.js');
		$this->context->smarty->assign('facebook_css_url', $this->_path.'css/blockcmfacebook.css');
		$html .= $this->context->smarty->fetch($this->local_path.'views/admin/_configure/preview.tpl');
		return $html;
	}

	public function hookDisplayProductTab()
	{
		global $link;

		$id_product = Tools::getValue('id_product');
		if (isset($id_product) && $id_product != '')
		{
			$product_infos = $this->context->controller->getProduct();
            $blockcmfacebook_rows = Configuration::get('blockcmfacebook_rows');
            $blockcmfacebook_width = Configuration::get('blockcmfacebook_width');
            $blockcmfacebook_theme = Configuration::get('blockcmfacebook_theme');
            $blockcmfacebook_orderby = Configuration::get('blockcmfacebook_orderby');
            $this->context->smarty->assign('blockcmfacebook_rows', $blockcmfacebook_rows);
            $this->context->smarty->assign('blockcmfacebook_width', $blockcmfacebook_width);
            $this->context->smarty->assign('blockcmfacebook_theme', $blockcmfacebook_theme);
            $this->context->smarty->assign('blockcmfacebook_orderby', $blockcmfacebook_orderby);
			$this->context->smarty->assign('linkproduct', $link->getProductLink($product_infos));

			return $this->display(__FILE__, 'blockcmfacebook.tpl');
		} else {
			return '';
		}
	}

	public function hookDisplayLeftColumn()
	{
		if ($this->page_name !== 'index')
			$this->_assignMedia();
		return $this->hookDisplayProductTab();
	}

	public function hookDisplayRightColumn()
	{
		if ($this->page_name !== 'index')
			$this->_assignMedia();
		return $this->hookDisplayProductTab();
	}

	public function hookHeader()
	{
		$this->page_name = Dispatcher::getInstance()->getController();
		if ($this->page_name == 'product')
			$this->_assignMedia();
	}

	protected function _assignMedia()
	{
		$this->context->controller->addCss(($this->_path).'css/blockcmfacebook.css');
		$this->context->controller->addJS(($this->_path).'blockcmfacebook.js');
	}

	public function renderForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('The number of rows'),
						'name' => 'blockcmfacebook_rows',
					),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Width'),
                        'name' => 'blockcmfacebook_width',
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Theme style'),
                        'name' => 'blockcmfacebook_theme',
                        'options' => array(
                            'query' => array(
                                array(
                                    'id_option' => 'light',
                                    'name' => $this->l('Light'),
                                ),
                                array(
                                    'id_option' => 'dark',
                                    'name' => $this->l('Dark'),
                                ),
                            ),
                            'id' => 'id_option',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Order comment by'),
                        'name' => 'blockcmfacebook_orderby',
                        'options' => array(
                            'query' => array(
                                array(
                                    'id_option' => 'social',
                                    'name' => $this->l('Social'),
                                ),
                                array(
                                    'id_option' => 'time',
                                    'name' => $this->l('Time'),
                                ),
                                array(
                                    'id_option' => 'reverse_time',
                                    'name' => $this->l('Reverse time'),
                                ),
                            ),
                            'id' => 'id_option',
                            'name' => 'name',
                        ),
                    ),
				),
				'submit' => array(
					'title' => $this->l('Save')
				)
			),
		);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table =  $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();

		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitModule';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}

	public function getConfigFieldsValues()
	{
		return array(
			'blockcmfacebook_rows' => Tools::getValue('blockcmfacebook_rows', Configuration::get('blockcmfacebook_rows')),
			'blockcmfacebook_theme' => Tools::getValue('blockcmfacebook_theme', Configuration::get('blockcmfacebook_theme')),
			'blockcmfacebook_width' => Tools::getValue('blockcmfacebook_width', Configuration::get('blockcmfacebook_width')),
			'blockcmfacebook_orderby' => Tools::getValue('blockcmfacebook_orderby', Configuration::get('blockcmfacebook_orderby')),
		);
	}
}
