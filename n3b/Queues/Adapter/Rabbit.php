<?php

namespace n3b\Queues\Adapter;

/**
 * Simple rabbitmq adapter with single exchange
 * and single channel
 */
class Rabbit
{
	const EXCHANGE_NAME = 'n3bQueuesExchange';

	protected $exchange;
	protected $queues;
	protected $channel;
	protected $connection;
	protected $config;

	public function __construct( array $config = array() )
	{
		if( ! class_exists( 'AMQPConnection' ) )
			throw new \Exception( 'You need to install php-amqp extension. See https://github.com/pdezwart/php-amqp' );

		$this->config = $config;
		$this->queues = array();
		$this->channel = null;
		$this->connection = null;
		$this->exchange = null;
	}

	public function __destruct()
	{
		$this->connection->disconnect();
	}

	/**
	 * @return \AMQPConnection|null
	 */
	protected function getConnection()
	{
		if( ! $this->connection instanceof \AMQPConnection )
		{
			$this->connection = new \AMQPConnection( $this->config );
			$this->connection->connect();
		}

		return $this->connection;
	}

	/**
	 * @return \AMQPChannel|null
	 */
	protected function getChannel()
	{
		if( ! $this->channel instanceof \AMQPChannel )
			$this->channel = new \AMQPChannel( $this->getConnection() );

		return $this->channel;
	}

	/**
	 * пока реализуем через единственный обменник
	 * @return \AMQPExchange
	 */
	protected function getExchange()
	{
		if( $this->exchange instanceof \AMQPExchange ) return $this->exchange;

		$this->exchange = new \AMQPExchange( $this->getChannel() );
		$this->exchange->setName( self::EXCHANGE_NAME );
		$this->exchange->setType( AMQP_EX_TYPE_DIRECT );
		$this->exchange->setFlags( AMQP_DURABLE );
		$this->exchange->declare();

		return $this->exchange;
	}

	/**
	 * получить очередь по имени
	 * @param $name
	 * @return \AMQPQueue
	 */
	protected function getQueue( $name )
	{
		if( isset( $this->queues[$name] ) )	return $this->queues[$name];

		$queue = new \AMQPQueue( $this->getChannel() );
		$queue->setName( $name );
		$queue->setFlags( AMQP_DURABLE );
		$queue->declare();
		$queue->bind( $this->getExchange()->getName(), $queue->getName() );

		return $this->queues[$name] = $queue;
	}

	/**
	 * бросить сообщение в очередь по имени
	 * ахтунг, если очереди нет на обменнике, сообщение пукнет в воду
	 * чтобы сего не произошло, необходимо хотя бы раз объявить очередь. $this->getQueue( $name )
	 *
	 * @param $data
	 * @param $queueName
	 * @return mixed
	 */
	public function enqueue( $data, $queueName )
	{
		try
		{
			return $this->getExchange()->publish(
				serialize( $data ),
				$queueName,
				AMQP_NOPARAM,
				array( 'delivery_mode' => 2 )
			);
		}
		catch( \Exception $e )
		{
			$this->runtimeException( $e );
		}
	}

	/**
	 * вытащить сообщение по имени очереди
	 *
	 * @param $queueName
	 * @param bool $autoAck
	 * @return mixed|null
	 * @throws \Exception
	 */
	public function dequeue( $queueName, $autoAck = false )
	{
		try
		{
			$msg = $this->getQueue( $queueName )->get( $autoAck ? AMQP_AUTOACK : AMQP_NOPARAM );

			return $msg === false ? null : unserialize( $msg->getBody() );
		}
		catch( \Exception $e )
		{
			$this->runtimeException( $e );
		}
	}

	/**
	 * acknowledge
	 *
	 * @param $queueName
	 * @param $deliveryTag
	 */
	public function ack( $queueName, $deliveryTag )
	{
		try
		{
			$this->getQueue( $queueName )->ack( $deliveryTag );
		}
		catch( \Exception $e )
		{
			$this->runtimeException( $e );
		}
	}

	/**
	 * вернуть сообщение в очередь
	 *
	 * @param $queueName
	 * @param $deliveryTag
	 */
	public function nack( $queueName, $deliveryTag )
	{
		try
		{
			$this->getQueue( $queueName )->nack( $deliveryTag, AMQP_REQUEUE );
		}
		catch( \Exception $e )
		{
			$this->runtimeException( $e );
		}
	}

	/**
	 * кол-во сообщений в очереди
	 *
	 * @param $queueName
	 * @return mixed
	 */
	public function count( $queueName )
	{
		try
		{
			return $this->getQueue( $queueName )->declare();
		}
		catch( \Exception $e )
		{
			$this->runtimeException( $e );
		}
	}

	/**
	 * удалить очередь со всем содержимым
	 *
	 * @param $queueName
	 * @return mixed
	 */
	public function deleteQueue( $queueName )
	{
		try
		{
			return $this->getQueue( $queueName )->delete();
		}
		catch( \Exception $e )
		{
			$this->runtimeException( $e );
		}
	}

	/**
	 * isConnected всегда === true, так что будем чистить сами
	 * https://github.com/pdezwart/php-amqp/issues/35
	 *
	 * @param \Exception $e
	 * @throws \Exception
	 */
	protected function runtimeException( \Exception $e )
	{
		// предположительно отвалилось соединение, очистим ресурсы для повторной инициализации
		$this->connection->disconnect();
		$this->connection = null;
		$this->channel = null;
		$this->exchange = null;
		$this->queues = array();

		throw $e;
	}
}
