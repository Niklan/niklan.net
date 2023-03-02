<?php declare(strict_types = 1);

namespace Drupal\niklan\Hook\Toolbar;

use Drupal\Core\Site\Settings;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Provides a development warning toolbar element.
 */
final class DevelopmentWarningToolbar {

  /**
   * Implements hook_toolbar().
   */
  public function __invoke(): array {
    if (!Settings::get('niklan_development_warning', FALSE)) {
      return [];
    }

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
        'library' => ['niklan/development-warning.toolbar'],
      ],
    ];

    return $items;
  }

}
