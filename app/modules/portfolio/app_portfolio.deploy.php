<?php

declare(strict_types=1);

/**
 * @file
 * Deploy hooks for app_portfolio module.
 */

use Drupal\app_portfolio\Hook\Deploy\Deploy0001;

/**
 * Change portfolio body format from 'markdown' to 'text'.
 */
function app_portfolio_deploy_0001(): string {
  return \Drupal::classResolver(Deploy0001::class)();
}
