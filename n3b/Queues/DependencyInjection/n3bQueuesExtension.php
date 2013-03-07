<?php

namespace n3b\Queues\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder,
	Symfony\Component\DependencyInjection\Loader\YamlFileLoader,
	Symfony\Component\HttpKernel\DependencyInjection\Extension,
	Symfony\Component\Config\FileLocator;


class n3bQueuesExtension extends Extension
{

	public function load(array $configs, ContainerBuilder $container)
	{
		$config = array();
		foreach( $configs as $subConfig ) $config = array_merge( $config, $subConfig );

		$loader = new YamlFileLoader( $container, new FileLocator(__DIR__ . '/../Resources/config') );
		$loader->load( 'config.yml' );

		$adapters = $container->getParameter( $this->getAlias() . '.adapters' );

		if( isset( $congif['adapter'] ) && $config['adapter'] )
		{
			$key = key( (array) $config['adapter'] );

			if( ! isset( $adapters[$key] ) )
				throw new \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException(
					sprintf( 'Undefined queue adapter %s', $key )
			);

			$arguments = array_merge( (array) $adapters[$key]['arguments'], $config['adapter'][$key] );
		}
		else
		{
			$key = $container->getParameter( $this->getAlias() . '.default_adapter' );
			$arguments = $adapters[$key]['arguments'];
		}

		$container->setParameter( $this->getAlias() . '.queue.class', $adapters[$key]['queue_class'] );

		if( $arguments )
		{
			foreach( $arguments as $key => $value )
				$container->setParameter( $this->getAlias() . '.queue_adapter.' . $key, $value );
		}

		$loader->load( 'adapter_' . $key . '.yml' );
		$loader->load( 'services.yml' );
	}

	public function getAlias()
	{
		return 'n3b_queues';
	}
}