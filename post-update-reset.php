<?php

/**
 * @file
 * Provides script to rollback post update record.
 *
 * This not actually rollback any changes was made in update, it's only delete
 * knowledge about it from Drupal. It will allows to re-run update again.
 *
 * Use it only for development purposes.
 */

use Drupal\Component\Render\FormattableMarkup;

/** @var \Drupal\Core\KeyValueStore\KeyValueStoreInterface $key_value */
$key_value = \Drupal::keyValue('deploy_hook');
$existing_updates = $key_value->get('existing_updates');
$messenger = \Drupal::messenger();

// The full post_update function name to rollback.
$post_update_name = 'niklan_deploy_0003';

if (!in_array($post_update_name, $existing_updates)) {
  $messenger->addStatus(new FormattableMarkup('The post update @name was not found.', [
    '@name' => $post_update_name,
  ]));
}
else {
  if (($key = array_search($post_update_name, $existing_updates)) !== false) {
    unset($existing_updates[$key]);
  }
  $key_value->set('existing_updates', $existing_updates);
  $messenger->addStatus(new FormattableMarkup('The update @name was cleaned. You can rerun it now.', [
    '@name' => $post_update_name,
  ]));
}
