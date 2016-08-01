<?php

namespace Dreamlex\TicketBundle\DependencyInjection;

use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class DreamlexTicketExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $this->registerDoctrineMapping($config);
    }


    /**
     * @param array $config
     */
    public function registerDoctrineMapping(array $config)
    {
        foreach ($config['class'] as $type => $class) {
            if (!class_exists($class)) {
                return;
            }
        }

        $collector = DoctrineCollector::getInstance();

        $collector->addAssociation($config['class']['messages'], 'mapManyToOne', array(
            'fieldName' => 'media',
            'targetEntity' => $config['class']['media'],
            'cascade' => array(
                'persist',
            ),
            'joinColumns' => array(
                array(
                    'name' => 'media_id',
                    'referencedColumnName' => 'id',
                    'nullable' => false,
                ),
            ),
        ));

    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return 'dreamlex_ticket';
    }


}
