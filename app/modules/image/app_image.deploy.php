<?php

declare(strict_types=1);

/**
 * @file
 * Deploy hooks for app_image module.
 */

use Drupal\app_image\Hook\Deploy\Deploy0001;
use Drupal\app_image\Hook\Deploy\Deploy0002;

/**
 * Delete dynamic image style security key from state.
 */
function app_image_deploy_0001(): string {
  return \Drupal::classResolver(Deploy0001::class)();
}

/**
 * Delete generated dynamic image style derivatives.
 */
function app_image_deploy_0002(): string {
  return \Drupal::classResolver(Deploy0002::class)();
}
