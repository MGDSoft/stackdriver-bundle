<?php

namespace MGDSoft\Stackdriver\DependencyInjection;

use Monolog\Logger;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('mgd_stackdriver');

        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('credentials_json_file')->defaultValue("%kernel.project_dir%/config/keys/google_service_account.json")->end()

                ->arrayNode('error_reporting')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->booleanNode('ignore_400')->defaultTrue()->end()
                    ->end()
                ->end()
                ->scalarNode('log_name')->defaultNull()->end()
                ->scalarNode('level')->defaultValue('info')->end()
            ->end();

        return $treeBuilder;
    }
}
