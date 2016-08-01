<?php

namespace Dreamlex\TicketBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('dreamlex_ticket');
        $rootNode
            ->children()
                ->arrayNode('class')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('messages')->defaultValue('Dreamlex\\TicketBundle\\Entity\\Messages')->end()
                            ->scalarNode('media')->defaultValue('Application\\Sonata\\MediaBundle\\Entity\\Media')->end()
                            ->scalarNode('user')->defaultValue('Application\\Sonata\\UserBundle\\Entity\\User')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }

}
