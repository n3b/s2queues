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
	}

	public function getName()
	{
		return $this->name;
	}

	public function enqueue( $data )
	{
		return $this->rep->enqueue( $this->name, $data );
	}

	public function dequeue()
	{
		return reset( $this->rep->dequeue( $this->name ) );
	}
}
