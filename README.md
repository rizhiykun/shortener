# Short links service

## Environment variables
You can set the following environment variables from environment:

To prevent simultaneous generation of short links `Symfony Lock`.

```dotenv
LOCK_SHORTLINK_TTL=5.0
```

Default url for short links

```dotenv
BASE_URL=http://short.link
```

Lock dsn for `Symfony Lock`
```dotenv
LOCK_DSN=redis://redis:6379
```

RMQ dsn for `Symfony Messenger`
```dotenv
MESSENGER_TRANSPORT_DSN=amqp://admin:password@rabbitmq:5672
```

Database dsn for `Doctrine`
```dotenv
DATABASE_URL="postgresql://postgres:password@postgres:5432/postgres"
```
All this environment up from environment https://github.com/rizhiykun/shortlink_environment.git

## Installation
make sure that 'make' is installed on your system
```bash
make build
```

