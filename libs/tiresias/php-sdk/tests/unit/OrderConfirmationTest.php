<?php

require_once(dirname(__FILE__) . '/../_support/TiresiasOrderBuyer.php');
require_once(dirname(__FILE__) . '/../_support/TiresiasOrderPurchasedItem.php');
require_once(dirname(__FILE__) . '/../_support/TiresiasOrderStatus.php');
require_once(dirname(__FILE__) . '/../_support/TiresiasOrder.php');

class OrderConfirmationTest extends \Codeception\TestCase\Test
{
	use \Codeception\Specify;

    /**
     * @var \UnitTester
     */
    protected $tester;

	/**
	 * @var TiresiasOrder
	 */
	protected $order;

	/**
	 * @var TiresiasAccount
	 */
	protected $account;

	/**
	 * @inheritdoc
	 */
	protected function _before()
	{
		$this->order = new TiresiasOrder();
		$this->account = new TiresiasAccount('platform-00000000');
	}

	/**
	 * Tests the matched order confirmation API call.
	 */
	public function testMatchedOrderConfirmation()
    {
		$result = TiresiasOrderConfirmation::send($this->order, $this->account, 'test123');

		$this->specify('successful matched order confirmation', function() use ($result) {
			$this->assertTrue($result);
		});
    }

	/**
	 * Tests the un-matched order confirmation API call.
	 */
	public function testUnMatchedOrderConfirmation()
	{
		$result = TiresiasOrderConfirmation::send($this->order, $this->account);

		$this->specify('successful un-matched order confirmation', function() use ($result) {
			$this->assertTrue($result);
		});
	}
}
