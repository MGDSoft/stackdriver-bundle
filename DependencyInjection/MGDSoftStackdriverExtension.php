<?php

namespace MGDSoft\Stackdriver\DependencyInjection;

use Google\Cloud\Logging\LoggingClient;
use MGDSoft\Stackdriver\Logger\Handler\StackdriverHandler;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class MGDSoftStackdriverExtension extends Extension
{
    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration();
    }

    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        $credentialsFile = $container->resolveEnvPlaceholders($config['credentials_json_file'], true);
        if (!file_exists($credentialsFile)){
            throw new \RuntimeException("Google Service account credentials are required");
        }

        $gcloudCrendentials= json_decode(file_get_contents($credentialsFile), true);

        $loggingClientOptions['keyFile']      = $gcloudCrendentials;
        $loggingClientOptions['projectId']    = $gcloudCrendentials['project_id'];
        // batch multiple logs into one single RPC calls:
        $loggingClientOptions['batchEnabled'] = true;

        $container->setDefinition('mgd_logging_client', new Definition(LoggingClient::class, [$loggingClientOptions]));

        $def = $container->getDefinition(StackdriverHandler::class);
        $def->replaceArgument(0, $config['level']);
        $def->replaceArgument(1, $container->resolveEnvPlaceholders($config['log_name'], true));
        $def->replaceArgument(2, $config['error_reporting']['enabled']);
        $def->replaceArgument(3, $config['error_reporting']['ignore_400']);
        $def->replaceArgument(4, $container->getDefinition('mgd_logging_client'));

    }
}
