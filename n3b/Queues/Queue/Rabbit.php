<?php

namespace n3b\Queues\Queue;

use n3b\Queues\Adapter\Rabbit as RabbitAdapter;

class Rabbit extends \SplQueue
{
	protected $adapter;
	protected $name;

	public function __construct( $name, RabbitAdapter $adapter )
	{
		$this->adapter = $adapter;
		$this->name = $name;
	}

	/**
	 * @param mixed $data
	 * @throws \Exception
	 */
	public function enqueue( $data )
	{
		$this->adapter->enqueue( $data, $this->getName() );
	}

	/**
	 * @return mixed|null
	 * @throws \Exception
	 */
	public function dequeue()
	{
		return $this->adapter->dequeue( $this->getName(), true );
	}

	/**
	 * @return mixed
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return int|mixed
	 * @throws \Exception
	 */
	public function count()
	{
		return $this->adapter->count( $this->getName() );
	}

	/**
	 * @return mixed
	 * @throws \Exception
	 */
	public function delete()
	{
		return $this->adapter->deleteQueue( $this->getName() );
	}

}
