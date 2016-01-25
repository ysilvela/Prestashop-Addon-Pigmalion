{*
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
*}

{if isset($tiresias_product) && is_object($tiresias_product)}
	<div class="tiresias_product" style="display:none">
		<span class="url">{$tiresias_product->getUrl()|escape:'htmlall':'UTF-8'}</span>
		<span class="product_id">{$tiresias_product->getProductId()|escape:'htmlall':'UTF-8'}</span>
		<span class="name">{$tiresias_product->getName()|escape:'htmlall':'UTF-8'}</span>
		{if $tiresias_product->getImageUrl() neq ''}
			<span class="image_url">{$tiresias_product->getImageUrl()|escape:'htmlall':'UTF-8'}</span>
		{/if}
		<span class="price">{$tiresias_product->getPrice()|escape:'htmlall':'UTF-8'}</span>
        <span class="list_price">{$tiresias_product->getListPrice()|escape:'htmlall':'UTF-8'}</span>
		<span class="price_currency_code">{$tiresias_product->getCurrencyCode()|escape:'htmlall':'UTF-8'}</span>
		<span class="availability">{$tiresias_product->getAvailability()|escape:'htmlall':'UTF-8'}</span>
		{foreach from=$tiresias_product->getCategories() item=category}
			<span class="category">{$category|escape:'htmlall':'UTF-8'}</span>
		{/foreach}
		{if $tiresias_product->getDescription() neq ''}
			<span class="description">{$tiresias_product->getDescription()|escape:'htmlall':'UTF-8'}</span>
		{/if}
		{if $tiresias_product->getBrand() neq ''}
			<span class="brand">{$tiresias_product->getBrand()|escape:'htmlall':'UTF-8'}</span>
		{/if}
		{if $tiresias_product->getDatePublished() neq ''}
			<span class="date_published">{$tiresias_product->getDatePublished()|escape:'htmlall':'UTF-8'}</span>
		{/if}
		{foreach from=$tiresias_product->getTags() item=tag}
            {if $tag neq ''}
            <span class="tag1">{$tag|escape:'htmlall':'UTF-8'}</span>
            {/if}
		{/foreach}
	</div>
    {if isset($tiresias_category) && is_object($tiresias_category)}
        <div class="tiresias_category" style="display:none">{$tiresias_category->category_string|escape:'htmlall':'UTF-8'}</div>
    {/if}
{/if}