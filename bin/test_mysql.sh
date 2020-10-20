#!/usr/bin/env bash

eval "docker-compose exec \
        --env DB_DRIVER='mysql' \
        --env DB_PREFIX='' \
        --env DB_HOST='mysql' \
        --env DB_PORT='3306' \
        --env DB_DATABASE='pepper' \
        --env DB_USERNAME='root' \
        --env DB_PASSWORD='' \
    pepper ./vendor/bin/phpunit \
        --group sqlite \
        --log-junit 'log-junit.xml' \
        $@"
