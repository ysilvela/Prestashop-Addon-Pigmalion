<?php

require_once(dirname(__FILE__).'/../_support/TiresiasProduct.php');
require_once(dirname(__FILE__).'/../_support/TiresiasOrder.php');

class ExportCollectionTest extends \Codeception\TestCase\Test
{
	/**
	 * @var \UnitTester
	 */
	protected $tester;

	/**
	 * Tests that the export collection does not accept string items.
	 */
	public function testCollectionValidationForString()
	{
		$this->setExpectedException('TiresiasException');
		$collection = new TiresiasExportProductCollection();
		$collection[] = 'invalid item type';
	}

	/**
	 * Tests that the export collection does not accept integer items.
	 */
	public function testCollectionValidationForInteger()
	{
		$this->setExpectedException('TiresiasException');
		$collection = new TiresiasExportProductCollection();
		$collection->append(1);
	}

	/**
	 * Tests that the export collection does not accept float items.
	 */
	public function testCollectionValidationForFloat()
	{
		$this->setExpectedException('TiresiasException');
		$collection = new TiresiasExportProductCollection();
		$collection->append(99.99);
	}

	/**
	 * Tests that the export collection does not accept array items.
	 */
	public function testCollectionValidationForArray()
	{
		$this->setExpectedException('TiresiasException');
		$collection = new TiresiasExportProductCollection();
		$collection[] = array('test');
	}

	/**
	 * Tests that the export collection does not accept stdClass items.
	 */
	public function testCollectionValidationForObject()
	{
		$this->setExpectedException('TiresiasException');
		$collection = new TiresiasExportProductCollection();
		$collection->append(new stdClass());
	}
}
