<?php

declare(strict_types=1);

namespace Drupal\niklan_dev\Hook\Toolbar;

use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Provides a development warning toolbar element.
 *
 * @ingroup toolbar
 */
final class DevelopmentWarningToolbar {

  /**
   * Implements hook_toolbar().
   */
  public function __invoke(): array {
    // Add a warning about using a dev site.
    $items['dev-site-warning'] = [
      '#weight' => 999,
    ];

    $label = new TranslatableMarkup('Development version');

    $items['dev-site-warning']['#type'] = 'toolbar_item';
    $items['dev-site-warning']['tab'] = [
      '#type' => 'inline_template',
      '#template' => '<div class="toolbar-warning">{{ label }}</div>',
      '#context' => [
        'label' => $label,
      ],
      '#attached' => [
        'library' => ['niklan_dev/development-warning.toolbar'],
      ],
    ];

    return $items;
  }

}
