# GoogleCloud Stackdriver monolog handler [![Build Status](https://travis-ci.com/MGDSoft/stackdriver-bundle.svg?branch=master)](https://travis-ci.com/MGDSoft/stackdriver-bundle)

### Installation

This bundle use auto recipes from https://github.com/symfony/recipes-contrib , to activate 

To enable this recipe needs enable with the following command 

```sh
composer config extra.symfony.allow-contrib true
```

Install the bundle...

```sh
composer req mgdsoft/stackdriver-bundle
```

Configure env var "GOOGLE_SERVICE_ACCOUNT" in your .env file.
And finally configure like simple monolog service handler and enjoy it.

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