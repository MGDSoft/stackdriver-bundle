# Stackdriver for google cloud [![Build Status](https://travis-ci.com/MGDSoft/stackdriver-bundle.svg?branch=master)](https://travis-ci.com/MGDSoft/stackdriver-bundle)

### Instalation

This bundle use auto recipes from https://github.com/symfony/recipes-contrib , to activate 

To enable this recipe enable it with 

```sh
composer config extra.symfony.allow-contrib true
```

Configure like simple monolog handler and enjoy it.

```yaml
# /config/packages/prod/monolog.yaml
monolog:
    handlers:
        stack_driver:
            type: service
            id: MGDSoft\Stackdriver\Logger\Handler
```

