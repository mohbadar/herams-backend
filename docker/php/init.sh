#!/bin/sh
touch /run/env.json &&
chown nobody:nobody /run/env.json &&
env &&
jq -s 'env+add' > /run/env.json &&
exec php-fpm7 --force-stderr --fpm-config /php-fpm.conf