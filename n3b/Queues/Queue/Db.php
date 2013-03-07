<?php

namespace n3b\Queues\Queue;

use n3b\Queues\Entity\QueueRepository;

class Db extends \SplQueue
{
	private $rep;
	private $name;

	public function __construct( $name, QueueRepository $rep )
	{
		$this->rep = $rep;
		$this->name = $name;
	}

	public function getName()
	{
		return $this->name;
	}

	public function enqueue( $data )
	{
		return $this->rep->enqueue( $this->name, $data );
	}

	/**
	 * @return bool|mixed
	 */
	public function dequeue()
	{
		$ret = $this->rep->dequeue( $this->name );

		return count( $ret ) ? reset( $ret ) : false;
	}
}
