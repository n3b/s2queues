<?php

namespace n3b\Queues\Queue;

use n3b\Queues\Adapter\Rabbit as RabbitAdapter;

class Rabbit extends \SplQueue
{
	private $adapter;
	private $name;

	public function __construct( $name, RabbitAdapter $adapter )
	{
		$this->adapter = $adapter;
		$this->name = $name;
	}

	public function enqueue( $value )
	{

	}

	public function dequeue()
	{

	}

	public function count()
	{

	}
}
