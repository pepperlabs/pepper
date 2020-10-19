#!/usr/bin/env bash

DATABASE_DRIVER='sqlite' \
DATABASE_PREFIX='' \
DATABASE_HOST=':memory:' \
./vendor/bin/phpunit --group sqlite --log-junit "log-junit.xml"
