<?php

require_once(dirname(__FILE__) . '/../_support/TiresiasAccountMetaDataIframe.php');

class AccountTest extends \Codeception\TestCase\Test
{
	use \Codeception\Specify;

    /**
     * @var \UnitTester
     */
    protected $tester;

	/**
	 * Tests the "isConnectedToTiresias" method for the TiresiasAccount class.
	 */
	public function testAccountIsConnected()
	{
		$account = new TiresiasAccount('platform-test');

		$this->specify('account is not connected', function() use ($account) {
			$this->assertFalse($account->isConnectedToTiresias());
		});

		$token = new TiresiasApiToken('sso', '123');
		$account->addApiToken($token);

		$token = new TiresiasApiToken('products', '123');
		$account->addApiToken($token);

		$this->specify('account is connected', function() use ($account) {
			$this->assertTrue($account->isConnectedToTiresias());
		});
	}

	/**
	 * Tests the "getApiToken" method for the TiresiasAccount class.
	 */
	public function testAccountApiToken()
	{
		$account = new TiresiasAccount('platform-test');

		$this->specify('account does not have sso token', function() use ($account) {
			$this->assertNull($account->getApiToken('sso'));
		});

		$token = new TiresiasApiToken('sso', '123');
		$account->addApiToken($token);

		$this->specify('account has sso token', function() use ($account) {
			$this->assertEquals('123', $account->getApiToken('sso')->getValue());
		});
	}

	/**
	 * Tests the "ssoLogin" method for the TiresiasAccount class.
	 */
	public function testAccountSingleSignOn()
	{
		$account = new TiresiasAccount('platform-test');
		$meta = new TiresiasAccountMetaDataIframe();

		$this->specify('account sso without api token', function() use ($account, $meta) {
			$this->assertFalse($account->ssoLogin($meta));
		});
	}
}
