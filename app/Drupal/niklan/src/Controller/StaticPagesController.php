<?php

declare(strict_types=1);

namespace Drupal\niklan\Controller;

/**
 * @todo Separate into different controllers.
 */
final class StaticPagesController {

  public function about(): array {
    return [
      '#theme' => 'niklan_about_page',
    ];
  }

  public function contact(): array {
    return [
      '#theme' => 'niklan_contact_page',
    ];
  }

  public function services(): array {
    return [
      '#theme' => 'niklan_services_page',
    ];
  }

  public function support(): array {
    return [
      '#theme' => 'niklan_support_page',
    ];
  }

}
