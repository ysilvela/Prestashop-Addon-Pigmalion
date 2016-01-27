<?php

require_once(dirname(__FILE__) . '/../_support/TiresiasOAuthClientMetaData.php');

class OauthTest extends \Codeception\TestCase\Test
{
	use \Codeception\Specify;

    /**
     * @var \UnitTester
     */
    protected $tester;

	/**
	 * Test the OAuth client authenticate without a authorize code.
	 */
	public function testOauthAuthenticateWithoutCode()
    {
		$meta = new TiresiasOAuthClientMetaData();
		$client = new TiresiasOAuthClient($meta);

		$this->specify('failed oauth authenticate', function() use ($client) {
			$this->setExpectedException('TiresiasException');
			$client->authenticate('');
		});
    }
}
