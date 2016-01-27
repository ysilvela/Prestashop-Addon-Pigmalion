<?php

require_once(dirname(__FILE__) . '/../_support/TiresiasAccountMetaDataBilling.php');
require_once(dirname(__FILE__) . '/../_support/TiresiasAccountMetaDataOwner.php');
require_once(dirname(__FILE__) . '/../_support/TiresiasAccountMetaData.php');

class AccountCreateTest extends \Codeception\TestCase\Test
{
	use \Codeception\Specify;

    /**
     * @var \UnitTester
     */
    protected $tester;

	/**
	 * Tests that new accounts can be created successfully.
	 */
	public function testCreatingNewAccount()
    {
		/** @var TiresiasAccount $account */
		/** @var TiresiasAccountMetaData $meta */
		$meta = new TiresiasAccountMetaData();
		$account = TiresiasAccount::create($meta);

		$this->specify('account was created', function() use ($account, $meta) {
			$this->assertInstanceOf('TiresiasAccount', $account);
			$this->assertEquals($meta->getPlatform() . '-' . $meta->getName(), $account->getName());
		});

		$this->specify('account has api token sso', function() use ($account, $meta) {
			$token = $account->getApiToken('sso');
			$this->assertInstanceOf('TiresiasApiToken', $token);
			$this->assertEquals('sso', $token->getName());
			$this->assertNotEmpty($token->getValue());
		});

		$this->specify('account has api token products', function() use ($account, $meta) {
			$token = $account->getApiToken('products');
			$this->assertInstanceOf('TiresiasApiToken', $token);
			$this->assertEquals('products', $token->getName());
			$this->assertNotEmpty($token->getValue());
		});

		$this->specify('account is connected to tiresias', function() use ($account, $meta) {
			$this->assertTrue($account->isConnectedToTiresias());
		});
    }
}
