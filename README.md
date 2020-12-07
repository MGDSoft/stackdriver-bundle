# GoogleCloud Stackdriver Bundle [![Build Status](https://travis-ci.com/MGDSoft/stackdriver-bundle.svg?branch=master)](https://travis-ci.com/MGDSoft/stackdriver-bundle)

Log all records in Stackdriver using this bundle. Some features

- Error reporting send notifications (by exception or only with log level error)
- Follow logs from same request with label requestId
- Create correct metadata from $_ENV vars
- Auto create logname ${gcloud_service}-symfony.log
- Track current user

### Installation

This bundle use auto recipes from https://github.com/symfony/recipes-contrib, to enable this recipe needs enable with the following command 

 

```sh
composer config extra.symfony.allow-contrib true
```

Install the bundle...

```sh
composer req mgdsoft/stackdriver-bundle
```

Configure env var "GOOGLE_SERVICE_ACCOUNT" in your .env file. Verify project_id is present in .json file

Finally configure like simple monolog service handler and enjoy it.

```yaml
# /config/packages/prod/monolog.yaml
monolog:
    handlers:
        stack_driver:
            type: service
            id: MGDSoft\Stackdriver\Logger\Handler
```

By default all errors are reported, if you want to disable reports see the config 

```sh
./bin/console deb:config MGDSoftStackdriverBundle
```

All pull request are welcome ;-)