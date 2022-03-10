<?php declare(strict_types=1);

namespace Tests\Unit\Logger;

use Google\Cloud\Core\Report\EmptyMetadataProvider;
use Google\Cloud\Logging\LoggingClient;
use Google\Cloud\Logging\PsrLogger;
use MGDSoft\Stackdriver\Logger\Handler\StackdriverHandler;
use Monolog\Logger;
use Monolog\Test\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Security;

class StackdriverHandlerTest extends TestCase
{
    public function testLevelLowerThanService()
    {
        list($handler, $logger) = $this->createStackDriverHandler();
        $logger->expects($this->never())->method('log');

        $handler->handle($this->getRecord(Logger::DEBUG, 'test'));
    }

    public function testOK()
    {
        list($handler, $logger) = $this->createStackDriverHandler();
        $logger->expects($this->any())->method('log')->will($this->returnCallback(function($level, $message, $data) {
            $this->assertEquals($level, 'ERROR');
            $this->assertIsArray($data['reportLocation'], 'ERROR');
            $this->assertEquals($data['@type'], 'type.googleapis.com/google.devtools.clouderrorreporting.v1beta1.ReportedErrorEvent');
        }));

        $handler->handle($this->getRecord(Logger::ERROR, 'test'));
    }

    public function testDisableReport()
    {
        list($handler, $logger) = $this->createStackDriverHandler(false);
        $logger->expects($this->any())->method('log')->will($this->returnCallback(function($level, $message, $data) {
            $this->assertEquals($level, 'ERROR');
            $this->assertArrayNotHasKey('reportLocation', $data);
        }));

        $handler->handle($this->getRecord(Logger::ERROR, 'test'));
    }

    public function testNotIgnore400Report()
    {
        list($handler, $logger) = $this->createStackDriverHandler(true, false);
        $logger->expects($this->any())->method('log')->will($this->returnCallback(function($level, $message, $data) {
            $this->assertEquals($level, 'ERROR');
            $this->assertArrayHasKey('reportLocation', $data);
        }));

        $record = $this->getRecord(Logger::ERROR, 'test', ['exception' => new NotFoundHttpException()]);
        $handler->handle($record);
    }

    public function testIgnore400Report()
    {
        list($handler, $logger) = $this->createStackDriverHandler();
        $logger->expects($this->any())->method('log')->will($this->returnCallback(function($level, $message, $data) {
            $this->assertEquals($level, 'WARNING');
            $this->assertArrayNotHasKey('reportLocation', $data);
        }));

        $record = $this->getRecord(Logger::ERROR, 'test', ['exception' => new NotFoundHttpException()]);
        $handler->handle($record);
    }

    private function createStackDriverHandler($enableReport = true, $errReportIgnore404 = true)
    {
        $security = $this->getMockBuilder(Security::class)->disableOriginalConstructor()->getMock();
        $logger = $this->getMockBuilder(PsrLogger::class)->disableOriginalConstructor()->getMock();

        $logger->expects($this->any())
            ->method('getMetadataProvider')
            ->willReturn(new EmptyMetadataProvider())
        ;

        $logging = $this->getMockBuilder(LoggingClient::class)->disableOriginalConstructor()
            ->getMock();
        $logging
            ->expects($this->any())
            ->method('psrLogger')
            ->willReturn($logger)
        ;

        $handler = new StackdriverHandler('info', null, $enableReport, $errReportIgnore404, $logging, $security);
        $handler->setFormatter($this->getIdentityFormatter());

        return [$handler, $logger];
    }
}