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
 * to contact@tiresias.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    BeTechnology Solutions Ltd <contact@tiresias.com>
 * @copyright 2013-2015 BeTechnology Solutions Ltd
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

/**
 * Helper class for sending product create/update/delete events to Tiresias.
 */
class TiresiasTaggingHelperProductOperation
{
	/**
	 * @var array runtime cache for products that have already been processed during this request to avoid sending the
	 * info to Tiresias many times during the same request. This will otherwise happen as PrestaShop will sometime invoke
	 * the hook callback methods multiple times when saving a product.
	 */
	private static $_processed_products = array();

	/**
	 * @var array stores a snapshot of the context object and shop context so it can be restored between processing all
	 * accounts. This is important as the accounts belong to different shops and languages and the context, that
	 * contains this information, is used internally in PrestaShop when generating urls.
	 */
	private $_context_snapshot;

	/**
	 * Sends a product create API request to Tiresias.
	 *
	 * @param Product $product the product that has been created.
	 */
	public function create(Product $product)
	{
		if (!Validate::isLoadedObject($product) || in_array($product->id, self::$_processed_products))
			return;

		self::$_processed_products[] = $product->id;
		foreach ($this->getAccountData() as $data)
		{
			list($account, $id_shop, $id_lang) = $data;

			$tiresias_product = $this->loadTiresiasProduct((int)$product->id, $id_lang, $id_shop);
			if (is_null($tiresias_product))
				continue;

			try
			{
				$op = new TiresiasOperationProduct($account);
				$op->addProduct($tiresias_product);
				$op->create();
			}
			catch (TiresiasException $e)
			{
				Tiresias::helper('tiresias_tagging/logger')->error(
					__CLASS__.'::'.__FUNCTION__.' - '.$e->getMessage(),
					$e->getCode(),
					get_class($product),
					(int)$product->id
				);
			}
		}
	}

	/**
	 * Sends a product update API request to Tiresias.
	 *
	 * @param Product $product the product that has been updated.
	 */
	public function update(Product $product)
	{
		if (!Validate::isLoadedObject($product) || in_array($product->id, self::$_processed_products))
			return;

		self::$_processed_products[] = $product->id;
		foreach ($this->getAccountData() as $data)
		{
			list($account, $id_shop, $id_lang) = $data;

			$tiresias_product = $this->loadTiresiasProduct((int)$product->id, $id_lang, $id_shop);
			if (is_null($tiresias_product))
				continue;

			try
			{
				$op = new TiresiasOperationProduct($account);
				$op->addProduct($tiresias_product);
				$op->update();
			}
			catch (TiresiasException $e)
			{
				Tiresias::helper('tiresias_tagging/logger')->error(
					__CLASS__.'::'.__FUNCTION__.' - '.$e->getMessage(),
					$e->getCode(),
					get_class($product),
					(int)$product->id
				);
			}
		}
	}

	/**
	 * Sends a product delete API request to Tiresias.
	 *
	 * @param Product $product the product that has been deleted.
	 */
	public function delete(Product $product)
	{
		if (!Validate::isLoadedObject($product) || in_array($product->id, self::$_processed_products))
			return;

		self::$_processed_products[] = $product->id;
		foreach ($this->getAccountData() as $data)
		{
			list($account) = $data;

			$tiresias_product = new TiresiasTaggingProduct();
			$tiresias_product->assignId($product);

			try
			{
				$op = new TiresiasOperationProduct($account);
				$op->addProduct($tiresias_product);
				$op->delete();
			}
			catch (TiresiasException $e)
			{
				Tiresias::helper('tiresias_tagging/logger')->error(
					__CLASS__.'::'.__FUNCTION__.' - '.$e->getMessage(),
					$e->getCode(),
					get_class($product),
					(int)$product->id
				);
			}
		}
	}

	/**
	 * Returns Tiresias accounts based on active shops.
	 *
	 * The result is formatted as follows:
	 *
	 * array(
	 *   array(object(TiresiasAccount), int(id_shop), int(id_lang))
	 * )
	 *
	 * @return TiresiasAccount[] the account data.
	 */
	protected function getAccountData()
	{
		$data = array();
		/** @var TiresiasTaggingHelperAccount $account_helper */
		$account_helper = Tiresias::helper('tiresias_tagging/account');
		foreach ($this->getContextShops() as $shop)
		{
			$id_shop = (int)$shop['id_shop'];
			$id_shop_group = (int)$shop['id_shop_group'];
			foreach (LanguageCore::getLanguages(true, $id_shop) as $language)
			{
				$id_lang = (int)$language['id_lang'];
				$account = $account_helper->find($id_lang, $id_shop_group, $id_shop);
				if ($account === null || !$account->isConnectedToTiresias())
					continue;

				$data[] = array($account, $id_shop, $id_lang);
			}
		}
		return $data;
	}

	/**
	 * Returns the shops that are affected by the current context.
	 *
	 * @return array list of shop data.
	 */
	protected function getContextShops()
	{
		if (_PS_VERSION_ >= '1.5' && Shop::isFeatureActive() && Shop::getContext() !== Shop::CONTEXT_SHOP)
		{
			if (Shop::getContext() === Shop::CONTEXT_GROUP)
				return Shop::getShops(true, Shop::getContextShopGroupID());
			else
				return Shop::getShops(true);
		}
		else
		{
			$ctx = Context::getContext();
			return array(
				(int)$ctx->shop->id => array(
					'id_shop' => (int)$ctx->shop->id,
					'id_shop_group' => (int)$ctx->shop->id_shop_group,
				),
			);
		}
	}

	/**
	 * Loads a Tiresias product model for given PS product ID, language ID and shop ID.
	 *
	 * @param int $id_product the PS product ID.
	 * @param int $id_lang the language ID.
	 * @param int $id_shop the shop ID.
	 * @return TiresiasTaggingProduct|null the product or null if could not be loaded.
	 */
	protected function loadTiresiasProduct($id_product, $id_lang, $id_shop)
	{
		$product = new Product($id_product, false, $id_lang, $id_shop);
		if (!Validate::isLoadedObject($product))
			return null;

		if (isset($product->visibility) && $product->visibility === 'none')
			return null;

		$this->makeContextSnapshot();

		$tiresias_product = new TiresiasTaggingProduct();
		$tiresias_product->loadData($this->makeContext($id_lang, $id_shop), $product);

		$this->restoreContextSnapshot();

		$validator = new TiresiasValidator($tiresias_product);
		if (!$validator->validate())
			return null;

		return $tiresias_product;
	}

	/**
	 * Stores a snapshot of the current context.
	 */
	protected function makeContextSnapshot()
	{
		$this->_context_snapshot = array(
			'shop_context' => (_PS_VERSION_ >= '1.5') ? Shop::getContext() : null,
			'context_object' => Context::getContext()->cloneContext()
		);
	}

	/**
	 * Restore the context snapshot to the current context.
	 */
	protected function restoreContextSnapshot()
	{
		if (!empty($this->_context_snapshot))
		{
			$original_context = $this->_context_snapshot['context_object'];
			$shop_context = $this->_context_snapshot['shop_context'];
			$this->_context_snapshot = null;

			$current_context = Context::getContext();
			$current_context->language = $original_context->language;
			$current_context->shop = $original_context->shop;
			$current_context->link = $original_context->link;
			$current_context->currency = $original_context->currency;

			if (_PS_VERSION_ >= '1.5')
			{
				Shop::setContext($shop_context, $current_context->shop->id);
				Dispatcher::$instance = null;
				if (method_exists('ShopUrl', 'resetMainDomainCache'))
					ShopUrl::resetMainDomainCache();
			}
		}
	}

	/**
	 * Modifies the current context and replaces the info related to shop, link, language and currency.
	 *
	 * We need this when generating the product data for the different shops and languages.
	 * The currency will be the first found for the shop, but it defaults to the PS default currency
	 * if no shop specific one is found.
	 *
	 * @param int $id_lang the language ID to add to the new context.
	 * @param int $id_shop the shop ID to add to the new context.
	 * @return Context the new context.
	 */
	protected function makeContext($id_lang, $id_shop)
	{
		if (_PS_VERSION_ >= '1.5')
		{
			// Reset the shop context to be the current processed shop. This will fix the "friendly url" format of urls
			// generated through the Link class.
			Shop::setContext(Shop::CONTEXT_SHOP, $id_shop);
			// Reset the dispatcher singleton instance so that the url rewrite setting is check on a shop basis when
			// generating product urls. This will fix the issue of incorrectly formatted urls when one shop has the
			// rewrite setting enabled and another does not.
			Dispatcher::$instance = null;
			if (method_exists('ShopUrl', 'resetMainDomainCache'))
			{
				// Reset the shop url domain cache so that it is re-initialized on a shop basis when generating product
				// image urls. This will fix the issue of the image urls having an incorrect shop base url when the
				// shops are configured to use different domains.
				ShopUrl::resetMainDomainCache();
			}

			foreach (Currency::getCurrenciesByIdShop($id_shop) as $row)
				if ($row['deleted'] === '0' && $row['active'] === '1')
				{
					$currency = new Currency($row['id_currency']);
					break;
				}
		}

		$context = Context::getContext();
		$context->language = new Language($id_lang);
		$context->shop = new Shop($id_shop);
		$context->link = new Link('http://', 'http://');
		$context->currency = isset($currency) ? $currency : Currency::getDefaultCurrency();

		return $context;
	}
}
