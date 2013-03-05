<?php

namespace n3b\Queues\Entity;

use Doctrine\ORM\EntityRepository;

class QueueRepository extends EntityRepository
{
	public function enqueue( $queueName, $object )
	{
		$msg = new QueueMessage();
		$msg->setName( $queueName );
		$msg->setData( $object );

		$this->getEntityManager()->persist( $msg );
		$this->getEntityManager()->flush();

		return $msg;
	}

	public function dequeue( $queueName, $limit = 1 )
	{
		$ret = array();

		if( $col = $this->findBy( array( 'name' => $queueName ), null, $limit ) )
		{
			foreach( $col as $entity )
			{
				$ret[] = $entity->getData();
				$this->getEntityManager()->remove( $entity );
			}

			$this->getEntityManager()->flush();
		}

		return $ret;
	}
}
