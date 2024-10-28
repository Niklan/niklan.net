<?php

declare(strict_types=1);

namespace Drupal\niklan\Controller;

/**
 * @todo Separate into different controllers.
 */
final class StaticPagesController {

  public function services(): array {
    return [
      '#theme' => 'niklan_services_page',
    ];
  }

}
