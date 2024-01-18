ARG PHP_TAG
FROM wodby/drupal-php:${PHP_TAG}

USER wodby
RUN sh -c "$(curl --location https://taskfile.dev/install.sh)" -- -d -b /usr/local/bin