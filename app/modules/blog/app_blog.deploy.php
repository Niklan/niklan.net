<?php

declare(strict_types=1);

/**
 * @file
 * Deploy hooks for app_blog module.
 */

use Drupal\app_blog\Hook\Deploy\Deploy0001;
use Drupal\app_blog\Hook\Deploy\Deploy0002;

/**
 * Create 301 redirects from /blog/{nid} to slug-based URLs.
 */
function app_blog_deploy_0001(array &$sandbox): string {
  return \Drupal::classResolver(Deploy0001::class)($sandbox);
}

/**
 * Set blog_entry body format to 'blog_article' where NULL.
 */
function app_blog_deploy_0002(): string {
  return \Drupal::classResolver(Deploy0002::class)();
}
