<?php declare(strict_types = 1);

/**
 * @file
 * Contains deploy hooks.
 */

use Drupal\niklan\Hook\Deploy\Deploy0001;
use Drupal\niklan\Hook\Deploy\Deploy0002;
use Drupal\niklan\Hook\Deploy\Deploy0003;
use Drupal\niklan\Hook\Deploy\Deploy0004;
use Drupal\niklan\Hook\Deploy\Deploy0005;

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

/**
 * Remove paragraphs.
 */
function niklan_deploy_0004(array &$sandbox): string {
  return \Drupal::classResolver(Deploy0004::class)($sandbox);
}

/**
 * Remove media files with references to an image.
 */
function niklan_deploy_0005(array &$sandbox): string {
  return \Drupal::classResolver(Deploy0005::class)($sandbox);
}
