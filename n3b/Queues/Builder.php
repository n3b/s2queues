<?php

namespace n3b\Queues;

class Builder
{
	protected $queues;
	protected $adapter;
	protected $queueClass;

	public function __construct( $adapter, $queueClass )
	{
		$this->queues = array();
		$this->adapter = $adapter;
		$this->queueClass = $queueClass;
	}

	/**
	 * @param $queueName
	 * @return \SplQueue
	 */
	public function get( $queueName )
	{
		if( ! isset( $this->queues[$queueName] ) )
			$this->queues[$queueName] = new $this->queueClass( $queueName, $this->adapter );

		return $this->queues[$queueName];
	}
}
