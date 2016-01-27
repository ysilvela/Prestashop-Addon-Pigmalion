<?php

class TiresiasAccountMetaData implements TiresiasAccountMetaDataInterface
{
	protected $owner;
	protected $billing;
	public function __construct()
	{
		$this->owner = new TiresiasAccountMetaDataOwner();
		$this->billing = new TiresiasAccountMetaDataBilling();
	}
	public function getTitle()
	{
		return 'My Shop';
	}
	public function getName()
	{
		return '00000000';
	}
	public function getPlatform()
	{
		return 'platform';
	}
	public function getFrontPageUrl()
	{
		return 'http://localhost';
	}
	public function getCurrencyCode()
	{
		return 'USD';
	}
	public function getLanguageCode()
	{
		return 'en';
	}
	public function getOwnerLanguageCode()
	{
		return 'en';
	}
	public function getOwner()
	{
		return $this->owner;
	}
	public function getBillingDetails()
	{
		return $this->billing;
	}
	public function getSignUpApiToken()
	{
		return 'abc123';
	}
}
