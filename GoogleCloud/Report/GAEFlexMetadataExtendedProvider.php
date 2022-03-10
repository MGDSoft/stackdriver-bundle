<?php

namespace MGDSoft\Stackdriver\GoogleCloud\Report;

use Google\Cloud\Core\Report\GAEFlexMetadataProvider;

/**
 * https://github.com/googleapis/google-cloud-php/pull/1407
 */
class GAEFlexMetadataExtendedProvider extends GAEFlexMetadataProvider
{
    protected function getTraceValue($server)
    {
        $traceId = substr($server['HTTP_X_CLOUD_TRACE_CONTEXT'], 0, 32);
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
