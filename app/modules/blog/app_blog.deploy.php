<?php

declare(strict_types=1);

/**
 * @file
 * Deploy hooks for app_blog module.
 */

use Drupal\app_blog\Hook\Deploy\Deploy0001;

/**
 * Create 301 redirects from /blog/{nid} to slug-based URLs.
 */
function app_blog_deploy_0001(array &$sandbox): string {
  return \Drupal::classResolver(Deploy0001::class)($sandbox);
}
