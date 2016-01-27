<?php

class TiresiasAccountMetaDataIframe implements TiresiasAccountMetaDataIframeInterface
{
	public function getFirstName()
	{
		return 'James';
	}
	public function getLastName()
	{
		return 'Kirk';
	}
	public function getEmail()
	{
		return 'james.kirk@example.com';
	}
	public function getLanguageIsoCode()
	{
		return 'en';
	}
	public function getLanguageIsoCodeShop()
	{
		return 'en';
	}
	public function getUniqueId()
	{
		return '123';
	}
	public function getPlatform()
	{
		return 'platform';
	}
	public function getVersionPlatform()
	{
		return '1.0.0';
	}
	public function getVersionModule()
	{
		return '1.0.0';
	}
	public function getPreviewUrlProduct()
	{
		return 'http://my.shop.com/products/product123?tiresiasdebug=true';
	}
	public function getPreviewUrlCategory()
	{
		return 'http://my.shop.com/products/category123?tiresiasdebug=true';
	}
	public function getPreviewUrlSearch()
	{
		return 'http://my.shop.com/search?query=red?tiresiasdebug=true';
	}
	public function getPreviewUrlCart()
	{
		return 'http://my.shop.com/cart?tiresiasdebug=true';
	}
	public function getPreviewUrlFront()
	{
		return 'http://my.shop.com?tiresiasdebug=true';
	}
	public function getShopName()
	{
		return 'Shop Name';
	}
}
