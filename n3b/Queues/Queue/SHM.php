<?php

namespace n3b\Queues\Queue;

use n3b\Queues\Adapter\SHM as SHMAdapter;

class SHM extends \SplQueue
{
	private $name;
	private $adapter;

	public function __construct( $name, SHMAdapter $adapter )
	{
		$this->name = $name;
		$this->adapter = $adapter;
	}
}
