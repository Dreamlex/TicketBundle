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
            ->scalarNode('user_entity')
            ->cannotBeEmpty()->end();

        $this->addMediaSection($rootNode);

        return $treeBuilder;
    }


    public function addMediaSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->scalarNode('media_entity')
                ->cannotBeEmpty()->end();
    }
}
