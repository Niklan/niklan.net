#!/bin/bash
# This is special wrapper for PHP executable inside wodby/php container.
# It's intended to be used instead of 'php' executable in PHP storm to be able
# run Functional tests, that require that user.
#
# @see https://i.imgur.com/pYZoXcx.png
#
# If you have error 'Directory "/opt/phpstorm-coverage" could not be created',
# then you should help it to be created:
# 1. $ sudo mkdir -p /opt/phpstorm-coverage
# 2. $ sudo chmod 777 /opt/phpstorm-coverage
#
# This script should be executable to work:
# $ chmod +x d4d-php.sh
sudo -u root -E sudo -u www-data -E /usr/local/bin/php "$@"
