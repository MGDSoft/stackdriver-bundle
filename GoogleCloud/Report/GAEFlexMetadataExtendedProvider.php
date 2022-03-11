<?php

namespace MGDSoft\Stackdriver\GoogleCloud\Report;

use Google\Cloud\Core\Report\GAEFlexMetadataProvider;
use MGDSoft\Stackdriver\Logger\Handler\StackdriverHandler;

/**
 * https://github.com/googleapis/google-cloud-php/pull/1407
 */
class GAEFlexMetadataExtendedProvider extends GAEFlexMetadataProvider
{
    protected function getTraceValue($server)
    {
        if (isset($server['HTTP_X_CLOUD_TRACE_CONTEXT'])) {
            $traceId = substr($server['HTTP_X_CLOUD_TRACE_CONTEXT'], 0, 32);
        } else {
            $traceId = StackdriverHandler::$requestId;
        }

        if (isset($server['GOOGLE_CLOUD_PROJECT'])) {
            return sprintf(
                'projects/%s/traces/%s',
                $server['GOOGLE_CLOUD_PROJECT'],
                $traceId
            );
        }

        return $traceId;
    }
}
