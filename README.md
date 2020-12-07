# Stackdriver for google cloud

Configure like simple monolog handler and enjoy it.

```yaml
# /config/packages/prod/monolog.yaml
monolog:
    handlers:
        stack_driver:
            type: service
            id: MGDSoft\Stackdriver\Logger\Handler
```

