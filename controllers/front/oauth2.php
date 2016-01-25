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
 * @property TiresiasTagging $module
 */
class TiresiasTaggingOauth2ModuleFrontController extends ModuleFrontController
{
	/**
	 * @inheritdoc
	 */
	public function initContent()
	{
		$id_lang = (int)Tools::getValue('language_id', $this->module->getContext()->language->id);
		if (($code = Tools::getValue('code')) !== false)
		{
			// The user accepted the authorization request.
			// The authorization server responded with a code that can be used to exchange for the access token.
			try
			{
				$meta = new TiresiasTaggingMetaOauth();
				$meta->setModuleName($this->module->name);
				$meta->setModulePath($this->module->getPath());
				$meta->loadData($this->module->getContext(), $id_lang);
				$account = TiresiasAccount::syncFromTiresias($meta, $code);

				if (!Tiresias::helper('tiresias_tagging/account')->save($account, $id_lang))
					throw new TiresiasException('Failed to save account.');

				$msg = $this->module->l('Account %s successfully connected to Tiresias.', 'oauth2');
				$this->redirectToModuleAdmin(array(
					'language_id' => $id_lang,
					'oauth_success' => sprintf($msg, $account->getName()),
				));
			}
			catch (TiresiasException $e)
			{
				Tiresias::helper('tiresias_tagging/logger')->error(
					__CLASS__.'::'.__FUNCTION__.' - '.$e->getMessage(),
					$e->getCode()
				);

				$msg = $this->module->l('Account could not be connected to Tiresias. Please contact Tiresias support.', 'oauth2');
				$this->redirectToModuleAdmin(array(
					'language_id' => $id_lang,
					'oauth_error' => $msg,
				));
			}
		}
		elseif (($error = Tools::getValue('error')) !== false)
		{
			$message_parts = array($error);
			if (($error_reason = Tools::getValue('error_reason')) !== false)
				$message_parts[] = $error_reason;
			if (($error_description = Tools::getValue('error_description')) !== false)
				$message_parts[] = urldecode($error_description);
			Tiresias::helper('tiresias_tagging/logger')->error(
				__CLASS__.'::'.__FUNCTION__.' - '.implode(' - ', $message_parts),
				200
			);
			// Prefer to show the error description sent from Tiresias to the user when something is wrong.
			// These messages are localized to users current back office language.
			if (!empty($error_description))
				$msg = urldecode($error_description);
			elseif (!empty($error_reason) && $error_reason === 'user_denied')
				$msg = $this->module->l('Account could not be connected to Tiresias. You rejected the connection request.', 'oauth2');
			else
				$msg = $this->module->l('Account could not be connected to Tiresias. Please contact Tiresias support.', 'oauth2');
			$this->redirectToModuleAdmin(array(
				'language_id' => $id_lang,
				'oauth_error' => $msg,
			));
		}
		$this->notFound();
	}

	/**
	 * Redirects the user to the module admin url if the current user is logged in as an admin in the back office.
	 * If the url cannot be found, then show the 404 page.
	 *
	 * @param array $query_params
	 */
	protected function redirectToModuleAdmin(array $query_params)
	{
		$admin_url = Tiresias::helper('tiresias_tagging/config')->getAdminUrl();
		if (!empty($admin_url))
		{
			$admin_url = TiresiasHttpRequest::replaceQueryParamsInUrl($query_params, $admin_url);
			Tools::redirect($admin_url, '');
			die;
		}
		$this->notFound();
	}

	/**
	 * Shows the 404 page to the user.
	 */
	protected function notFound()
	{
		if (_PS_VERSION_ < '1.5')
			Tools::display404Error();
		else
			Controller::getController('PageNotFoundController')->run();
	}
}
