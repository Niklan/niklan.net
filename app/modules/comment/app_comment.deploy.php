<?php

declare(strict_types=1);

/**
 * @file
 * Deploy hooks for app_comment module.
 */

use Drupal\app_comment\Hook\Deploy\Deploy0001;

/**
 * Unify comment body format to 'comments'.
 */
function app_comment_deploy_0001(array &$sandbox): string {
  return \Drupal::classResolver(Deploy0001::class)($sandbox);
}
