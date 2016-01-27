<?php

require_once(dirname(__FILE__) . '/../_support/TiresiasProduct.php');
require_once(dirname(__FILE__) . '/../_support/TiresiasOrder.php');

class HistoryExportTest extends \Codeception\TestCase\Test
{
	use \Codeception\Specify;

    /**
     * @var \UnitTester
     */
    protected $tester;

	/**
	 * @var TiresiasAccount
	 */
	protected $account;

	/**
	 * @inheritdoc
	 */
	protected function _before()
	{
		$this->account = new TiresiasAccount('platform-00000000');
		// The first 16 chars of the SSO token are used as the encryption key.
		$token = new TiresiasApiToken('sso', '01098d0fc84ded7c4226820d5d1207c69243cbb3637dc4bc2a216dafcf09d783');
		$this->account->addApiToken($token);
	}

	/**
	 * Tests that product history data can be exported.
	 */
	public function testProductHistoryExport()
	{
		$collection = new TiresiasExportProductCollection();
		$collection[] = new TiresiasProduct();
		$cipher_text = TiresiasExporter::export($this->account, $collection);

		$this->specify('verify encrypted product export', function() use ($collection, $cipher_text) {
			$cipher = new TiresiasCipher();
			$cipher->setSecret('01098d0fc84ded7c');
			$cipher->setIV(substr($cipher_text, 0, 16));
			$plain_text = $cipher->decrypt(substr($cipher_text, 16));

			$this->assertEquals($collection->getJson(), $plain_text);
		});
	}

	/**
	 * Tests that order history data can be exported.
	 */
    public function testOrderHistoryExport()
    {
		$collection = new TiresiasExportOrderCollection();
		$collection->append(new TiresiasOrder());
		$cipher_text = TiresiasExporter::export($this->account, $collection);

		$this->specify('verify encrypted order export', function() use ($collection, $cipher_text) {
			$cipher = new TiresiasCipher();
			$cipher->setSecret('01098d0fc84ded7c');
			$cipher->setIV(substr($cipher_text, 0, 16));
			$plain_text = $cipher->decrypt(substr($cipher_text, 16));

			$this->assertEquals($collection->getJson(), $plain_text);
		});
    }
}
