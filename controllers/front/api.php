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
 * Base controller for all Tiresias API front controllers.
 *
 * @property TiresiasTagging $module
 * @property Context $context
 */
abstract class TiresiasTaggingApiModuleFrontController extends ModuleFrontController
{
	/**
	 * @var int the amount of items to fetch.
	 */
	public $limit = 100;

	/**
	 * @var int the offset of items to fetch.
	 */
	public $offset = 0;

	/**
	 * @inheritdoc
	 */
	public function __construct()
	{
		parent::__construct();

		if (($limit = Tools::getValue('limit')) !== false && !empty($limit))
			$this->limit = (int)$limit;

		if (($offset = Tools::getValue('offset')) !== false && !empty($offset))
			$this->offset = (int)$offset;
	}

	/**
	 * Encrypts and outputs the data and ends the application flow.
	 * Only send the response if we can encrypt it, i.e. we have an shared encryption secret with tiresias.
	 *
	 * @param TiresiasExportCollectionInterface $collection the data collection to output as encrypted response.
	 */
	public function encryptOutput(TiresiasExportCollectionInterface $collection)
	{
		/** @var TiresiasAccount $account */
		$account = Tiresias::helper('tiresias_tagging/account')->find($this->module->getContext()->language->id);
		if ($account && $account->isConnectedToTiresias())
		{
			$cipher_text = TiresiasExporter::export($account, $collection);
			echo $cipher_text;
		}
		// It is important to stop the script execution after the export,
		// in order to avoid any additional data being outputted.
		die();
	}
}
