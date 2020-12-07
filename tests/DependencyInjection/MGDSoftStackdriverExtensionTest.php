<?php

namespace MGDSoft\Stackdriver\tests\DependencyInjection;

use Google\Cloud\Logging\LoggingClient;
use MGDSoft\Stackdriver\DependencyInjection\Configuration;
use MGDSoft\Stackdriver\Logger\Handler\StackdriverHandler;
use MGDSoft\Stackdriver\MGDSoftStackdriverBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MGDSoftStackdriverExtensionTest extends TestCase
{
    public function testOKDefaultRecipe()
    {
        $configuration = new Configuration();
        $processor = new Processor();

        $configDefault = $processor->processConfiguration(
            $configuration,
            ['mgd_stackdriver' => ['credentials_json_file' => __DIR__ . '/../recipe/google_service_account.json']]
        );

        $container = $this->getContainerForConfig([
            $configDefault
        ]);

        $this->assertNotNull($container->hasDefinition('mgd_logging_client'));
        $this->assertNotNull($container->hasDefinition(StackdriverHandler::class));
    }

    private function getContainerForConfig(array $configs)
    {
        $container = $this->getContainerForConfigLoad($configs);
        $container->compile();

        return $container;
    }

    private function getContainerForConfigLoad(array $configs)
    {
        $bundle = new MGDSoftStackdriverBundle();
        $extension = $bundle->getContainerExtension();

        $container = new ContainerBuilder();
        $container->registerExtension($extension);
        $extension->load($configs, $container);

        $bundle->build($container);
        return $container;
    }
}