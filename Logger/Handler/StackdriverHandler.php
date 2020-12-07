<?php

namespace MGDSoft\Stackdriver\Logger\Handler;

use Google\Cloud\Logging\LoggingClient;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\PsrHandler;
use Monolog\Logger;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class StackdriverHandler extends PsrHandler
{
    protected $logger;
    protected $projectId;
    protected $security;
    protected $errorReportingEnabled;
    protected $errorReportingIgnore400;

    static protected $requestId = null;

    public function __construct(
        $level,
        Security $security,
        LoggingClient $loggingClient,
        $logName = null,
        $errorReportingEnabled = true,
        $errorReportingIgnore400 = true,
        $loggerOptions = [],
        $bubble = true
    )
    {
        if (!$logName) {
            $logName = ($_ENV['GAE_SERVICE'] ?? 'local') . '-symfony.log';
        }

        $this->logger                  = $loggingClient->psrLogger($logName, $loggerOptions);
        $this->security                = $security;
        $this->errorReportingEnabled   = $errorReportingEnabled;
        $this->errorReportingIgnore400 = $errorReportingIgnore400;

        if (!static::$requestId) {
            static::$requestId = uniqid(date("Y/m/d-H:i:s-"));
        }

        parent::__construct($this->logger, Logger::toMonologLevel($level), $bubble);
    }

    public function handle(array $record): bool
    {
        if (!$this->isHandling($record)) {
            return false;
        }

        $record = $this->setStackDriverOptionsFromRecord($record);
        $record = $this->setReportError($record);

        $this->logger->log(
            $record['level_name'],
            $this->getFormatter()->format($record),
            $record['context']
        );

        return false === $this->bubble;
    }

    protected function setStackDriverOptionsFromRecord(array $record): array
    {
        $metadataGCloud = $this->logger->getMetadataProvider();

        $userName = 'unknown';
        if (($user = $this->security->getUser()) && $user instanceof UserInterface) {
            $userName = $user->getUsername();
        }

        $record['context']['stackdriverOptions'] = [
            'labels'   => [
                'channel'   => $record['channel'],
                'user'      => $userName,
                'requestId' => static::$requestId,
            ],
            'resource' => [
                'type' => 'gae_app',
                'labels' => [
                    'proyect_id' => $metadataGCloud->projectId() ?? 'local',
                    'version_id' => $metadataGCloud->versionId() ?? 'local',
                    'module_id'  => $metadataGCloud->serviceId() ?? 'local',
                ]
            ],
        ];

        return $record;
    }

    protected function setReportError(array $record): array
    {

        if ($record['level'] < Logger::ERROR || !$this->errorReportingEnabled){
            return $record;
        }

        $ex = new \Exception($this->getFormatter()->format($record));

        if (isset($record['context']['exception'])) {
            $ex = $record['context']['exception'];
        }

        if ($this->errorReportingIgnore400
            && $ex instanceof HttpException && $ex->getStatusCode() >= 400 && $ex->getStatusCode() < 500
        ) {
            return $record;
        }

        $record['context']['reportLocation'] = [
            'filePath'   => $ex->getFile(),
            'lineNumber' => $ex->getLine(),
        ];

        $record['context']['@type'] = 'type.googleapis.com/google.devtools.clouderrorreporting.v1beta1.ReportedErrorEvent';

        $metadataGCloud = $this->logger->getMetadataProvider();

        $record['serviceContext'] = [
            'service' => $metadataGCloud->serviceId(),
            'version' => $metadataGCloud->versionId(),
        ];

        return $record;
    }

    protected function getDefaultFormatter()
    {
        return new LineFormatter("%channel%: %message%\n");
    }
}