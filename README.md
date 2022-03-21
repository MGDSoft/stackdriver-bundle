# GoogleCloud Stackdriver Bundle [![Build Status](https://travis-ci.com/MGDSoft/stackdriver-bundle.svg?branch=master)](https://app.travis-ci.com/github/MGDSoft/stackdriver-bundle)

Log all records in Stackdriver using this bundle. Some features

- Error reporting send notifications (by exception or log level error)
- Create correct metadata from $_ENV vars
- Auto create logname ${gcloud_service}-symfony.log
- track logs from same request with label requestId
- Track current user

### Installation

This bundle use auto recipes from https://github.com/symfony/recipes-contrib, to enable execute 

```sh
composer config extra.symfony.allow-contrib true
```

Install the bundle...

```sh
composer req mgdsoft/stackdriver-bundle
```

The bundle  will be configured only for prod environment see **packages/prod/mgdsoft_stackdriver.yaml** for more info.

Inside appengine credentials are configured auto, but to test in local you must set **mgdsoft_stackdriver.credentials_json_file**
  
Finally configure like simple monolog service handler and enjoy it.  

```yaml
# /config/packages/prod/monolog.yaml
monolog:
    handlers:
        stack_driver:
            type: service
            id: MGDSoft\Stackdriver\Logger\Handler
```

By default all errors are reported, if you want to disable update bundle config 

```yaml
#./bin/console config:dump-reference MGDSoftStackdriverBundle

mgdsoft_stackdriver:
    credentials_json_file:  null
    log_name:               null
    level:                  info
    error_reporting:
        enabled:              true
        ignore_400:           true
```

All pull request are welcome ;-)
