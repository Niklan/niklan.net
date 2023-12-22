<?php declare(strict_types = 1);

/**
 * @file
 * Contains deploy hooks.
 */

use Drupal\niklan\Hook\Deploy\Deploy0001;
use Drupal\niklan\Hook\Deploy\Deploy0002;
use Drupal\niklan\Hook\Deploy\Deploy0003;

/**
 * Migrate contact submission values into native fields.
 */
function niklan_deploy_0001(array &$sandbox): string {
  return \Drupal::classResolver(Deploy0001::class)($sandbox);
}

/**
 * Provides initial External ID value for existing content.
 */
function niklan_deploy_0002(array &$sandbox): string {
  return \Drupal::classResolver(Deploy0002::class)($sandbox);
}

/**
 * Calculates checksum for files.
 */
function niklan_deploy_0003(array &$sandbox): string {
  return \Drupal::classResolver(Deploy0003::class)($sandbox);
}
