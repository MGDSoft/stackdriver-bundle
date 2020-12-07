<?php

namespace MGDSoft\Stackdriver\DependencyInjection;

use MGDSoft\Stackdriver\Logger\Handler\StackdriverHandler;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class MGDSoftStackdriverExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        $container->getDefinition(StackdriverHandler::class)->replaceArgument(0, $config['credentials_json_file']);
        $container->getDefinition(StackdriverHandler::class)->replaceArgument(1, $config['level']);
        $container->getDefinition(StackdriverHandler::class)->replaceArgument(3, $config['log_name']);
        $container->getDefinition(StackdriverHandler::class)->replaceArgument(4, $config['error_reporting']['enabled']);
        $container->getDefinition(StackdriverHandler::class)->replaceArgument(5, $config['error_reporting']['ignore_400']);
    }
}
