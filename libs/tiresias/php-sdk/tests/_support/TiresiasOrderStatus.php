<?php

class TiresiasOrderStatus implements TiresiasOrderStatusInterface
{
	public function getCode()
	{
		return 'completed';
	}

	public function getLabel()
	{
		return 'Completed';
	}
}
