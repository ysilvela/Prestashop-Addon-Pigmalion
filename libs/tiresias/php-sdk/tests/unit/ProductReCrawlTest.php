<?php

require_once(dirname(__FILE__) . '/../_support/TiresiasProduct.php');

class ProductReCrawlTest extends \Codeception\TestCase\Test
{
	use \Codeception\Specify;

    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * Tests that product re-crawl API requests cannot be made without an API token.
     */
    public function testSendingProductReCrawlWithoutApiToken()
    {
		$account = new TiresiasAccount('platform-00000000');
        $product = new TiresiasProduct();

        $this->setExpectedException('TiresiasException');
        TiresiasProductReCrawl::send($product, $account);
    }

	/**
	 * Tests that product re-crawl API requests can be made.
	 */
	public function testSendingProductReCrawl()
    {
		$account = new TiresiasAccount('platform-00000000');
		$product = new TiresiasProduct();
		$token = new TiresiasApiToken('products', '01098d0fc84ded7c4226820d5d1207c69243cbb3637dc4bc2a216dafcf09d783');
		$account->addApiToken($token);

		$result = TiresiasProductReCrawl::send($product, $account);

		$this->specify('successful product re-crawl', function() use ($result) {
			$this->assertTrue($result);
		});
    }

    /**
     * Tests that batch product re-crawl API requests cannot be made without an API token.
     */
    public function testSendingBatchProductReCrawlWithoutApiToken()
    {
		$account = new TiresiasAccount('platform-00000000');
        $product = new TiresiasProduct();
        $collection = new TiresiasExportProductCollection();
        $collection[] = $product;

        $this->setExpectedException('TiresiasException');
        TiresiasProductReCrawl::sendBatch($collection, $account);
    }

    /**
     * Tests that batch product re-crawl API requests can be made.
     */
    public function testSendingBatchProductReCrawl()
    {
		$account = new TiresiasAccount('platform-00000000');
        $product = new TiresiasProduct();
        $collection = new TiresiasExportProductCollection();
        $collection[] = $product;
		$token = new TiresiasApiToken('products', '01098d0fc84ded7c4226820d5d1207c69243cbb3637dc4bc2a216dafcf09d783');
		$account->addApiToken($token);

        $result = TiresiasProductReCrawl::sendBatch($collection, $account);

        $this->specify('successful batch product re-crawl', function() use ($result) {
            $this->assertTrue($result);
        });
    }
}
