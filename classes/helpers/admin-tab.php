<?php
/**
 * 2013-2015 BeTechnology Solutions Ltd
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@betechnology.es so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    BeTechnology Solutions Ltd <info@betechnology.es>
 * @copyright 2013-2015 BeTechnology Solutions Ltd
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

/**
 * Helper class for managing the tab added to the admin section.
 */
class TiresiasTaggingHelperAdminTab
{
	/**
	 * @var array translations for the Tiresias `Personalization` menu item in PS 1.5.
	 */
	protected static $item_translations = array(
		'de' => 'Personalisierung',
		'fr' => 'Personnalisation',
		'es' => 'Personalización',
	);

	/**
	 * Installs the Admin Tab in PS backend.
	 * Only for PS >= 1.5.
	 *
	 * @return bool true on success, false otherwise.
	 */
	public function install()
	{
		if (_PS_VERSION_ < '1.5')
			return true;

		$languages = Language::getLanguages(true);
		if (empty($languages))
			return false;

		/** @var TabCore $tab */
		$tab = new Tab();
		$tab->active = 1;
		$tab->class_name = 'AdminTiresias';
		$tab->name = array();
		foreach ($languages as $lang)
			$tab->name[$lang['id_lang']] = 'Tiresias';

		$tab->id_parent = 0;
		$tab->module = 'tiresiastagging';
		$added = $tab->add();

		// For PS 1.6 it is enough to have the main menu, for PS 1.5 we need a sub-menu.
		if ($added && _PS_VERSION_ < '1.6')
		{
			$tab = new Tab();
			$tab->active = 1;
			$tab->class_name = 'AdminTiresiasPersonalization';
			$tab->name = array();
			foreach ($languages as $lang)
			{
				if (isset(self::$item_translations[$lang['iso_code']]))
					$tab->name[$lang['id_lang']] = self::$item_translations[$lang['iso_code']];
				else
					$tab->name[$lang['id_lang']] = 'Personalization';
			}
			$tab->id_parent = (int)Tab::getIdFromClassName('AdminTiresias');
			$tab->module = 'tiresiastagging';
			$added = $tab->add();
		}

		return $added;
	}

	/**
	 * Uninstalls the Admin Tab from PS backend.
	 * Only for PS >= 1.5.
	 *
	 * @return bool true on success false otherwise.
	 */
	public function uninstall()
	{
		PrestaShopLogger::addLog('admin-tab.uninstall. Entramos en la funcion uninstall. ', 1);
		if (_PS_VERSION_ < '1.5')
			return true;

		foreach (array('AdminTiresias', 'AdminTiresiasPersonalization') as $tab_name)
		{
			$id_tab = (int)Tab::getIdFromClassName($tab_name);
			if ($id_tab)
			{
				/** @var TabCore $tab */
				$tab = new Tab($id_tab);
				$tab->delete();
			}
		}

		return true;
	}
} 