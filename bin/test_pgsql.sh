#!/usr/bin/env bash

eval "docker-compose exec \
        --env DB_DRIVER='pgsql' \
        --env DB_PREFIX='' \
        --env DB_HOST='postgres' \
        --env DB_PORT='5432' \
        --env DB_DATABASE='pepper' \
        --env DB_USERNAME='root' \
        --env DB_PASSWORD='' \
    pepper ./vendor/bin/phpunit \
        --group pgsql \
        --log-junit 'log-junit.xml' \
        $@"
