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
 * Helper class for managing the link between Prestashop shopping carts and Tiresias users.
 * This link is used to create server side order confirmations through the Tiresias REST API.
 */
class TiresiasTaggingHelperCustomer
{
	const TABLE_NAME = 'tiresiastagging_customer_link';
	const COOKIE_NAME = '2c_cId';

	/**
	 * Returns the reference table name.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return _DB_PREFIX_.self::TABLE_NAME;
	}

	/**
	 * Creates the reference table in db if it does not exist.
	 *
	 * @return bool
	 */
	public function createTable()
	{		
		$table = self::getTableName();
		PrestaShopLogger::addLog('customer.createTable. Se crea la tabla con nombre ' .$table, 1);
		$sql = 'CREATE TABLE IF NOT EXISTS `'.$table.'` (
			`id_cart` INT(10) UNSIGNED NOT NULL,
			`id_tiresias_customer` VARCHAR(255) NOT NULL,
			`date_add` DATETIME NOT NULL,
			`date_upd` DATETIME NULL,
			PRIMARY KEY (`id_cart`, `id_tiresias_customer`)
		) ENGINE '._MYSQL_ENGINE_;
		return Db::getInstance()->execute($sql);
	}

	/**
	 * Drops the reference table from db if it exists.
	 *
	 * @return bool
	 */
	public function dropTable()
	{
		$table = self::getTableName();
		return Db::getInstance()->execute('DROP TABLE IF EXISTS `'.$table.'`');
	}

	/**
	 * Updates the current customers Tiresias ID in the reference table.
	 *
	 * @return bool true if updated correctly and false otherwise.
	 */
	public function updateTiresiasId()
	{
		PrestaShopLogger::addLog('customer.createTable. Actualizamos el campo TiresiasId ', 1);
		$context = Context::getContext();
		if (empty($context->cart->id))
			return false;

		$id_tiresias_customer = $this->readCookieValue();
		if (empty($id_tiresias_customer))
			return false;

		$table = self::getTableName();
		$id_cart = (int)$context->cart->id;
		$id_tiresias_customer = pSQL($id_tiresias_customer);
		$where = '`id_cart` = '.$id_cart.' AND `id_tiresias_customer` = "'.$id_tiresias_customer.'"';
		$existing_link = Db::getInstance()->getRow('SELECT * FROM `'.$table.'` WHERE '.$where);
		if (empty($existing_link))
		{
			$data = array(
				'id_cart' => $id_cart,
				'id_tiresias_customer' => $id_tiresias_customer,
				'date_add' => date('Y-m-d H:i:s')
			);
			if (_PS_VERSION_ >= '1.5')
				return Db::getInstance()->insert($table, $data, false, true, Db::INSERT, false);
			else
				return Db::getInstance()->autoExecute($table, $data, 'INSERT');
		}
		else
		{
			$data = array(
				'date_upd' => date('Y-m-d H:i:s')
			);
			if (_PS_VERSION_ >= '1.5')
				return Db::getInstance()->update($table, $data, $where, 0, false, true, false);
			else
				return Db::getInstance()->autoExecute($table, $data, 'UPDATE', $where);
		}
	}

	/**
	 * Returns the customers Tiresias ID.
	 *
	 * @param Order $order the order to get the customer from.
	 * @return bool|string the customers Tiresias ID or false if not found.
	 */
	public function getTiresiasId(Order $order)
	{
		$table = self::getTableName();
		$id_cart = (int)$order->id_cart;
		$sql = 'SELECT `id_tiresias_customer` FROM `'.$table.'` WHERE `id_cart` = '.$id_cart.' ORDER BY `date_add` ASC';
		return Db::getInstance()->getValue($sql);
	}

	/**
	 * Reads the Tiresias cookie value and returns it.
	 *
	 * @return null the cookie value, or null if not set.
	 */
	protected function readCookieValue()
	{
		// We use the $_COOKIE global directly here, instead of the Prestashop cookie class, as we are accessing a
		// tiresias cookie that have been set by the JavaScript loaded from tiresias.com. We read it to keep a mapping of
		// the Tiresias user ID and the Prestashop user ID so we can identify which user actually completed an order.
		// We do this for tracking whether or not to send abandoned cart emails.
		return isset($_COOKIE[self::COOKIE_NAME])
			? $_COOKIE[self::COOKIE_NAME]
			: null;
	}
}
