#!/usr/bin/env bash

eval "docker-compose exec -T \
        --env DB_DRIVER='sqlite' \
        --env DB_PREFIX='' \
        --env DB_DATABASE=':memory:' \
    pepper ./vendor/bin/phpunit \
        --group sqlite \
        --log-junit 'log-junit.xml' \
        $@"
