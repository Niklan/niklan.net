<?php declare(strict_types = 1);

/**
 * @file
 * Contains deploy hooks.
 */

use Drupal\niklan\Hook\Deploy\Deploy0001;

/**
 * Migrate contact submission values into native fields.
 */
function niklan_deploy_0001(array &$sandbox): string {
  return \Drupal::classResolver(Deploy0001::class)($sandbox);
}
