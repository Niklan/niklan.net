<?php declare(strict_types = 1);

namespace Drupal\niklan\Controller;

/**
 * Provides a common controller for static pages.
 */
final class StaticPagesController {

  /**
   * Prepares content for '/about' page.
   *
   * @return array
   *   A contents of the page.
   */
  public function about(): array {
    return [
      '#theme' => 'niklan_about_page',
    ];
  }

  /**
   * Prepares content for '/contact' page.
   *
   * @return array
   *   A contents of the page.
   *
   * @see \Drupal\niklan\EventSubscriber\RouteSubscriber::onAlterRoutes()
   */
  public function contact(): array {
    return [
      '#theme' => 'niklan_contact_page',
    ];
  }

  /**
   * Prepares content for '/services' page.
   *
   * @return array
   *   A contents of the page.
   */
  public function services(): array {
    return [
      '#theme' => 'niklan_services_page',
    ];
  }

  /**
   * Prepares content for '/support' page.
   *
   * @return array
   *   A contents of the page.
   */
  public function support(): array {
    return [
      '#theme' => 'niklan_support_page',
    ];
  }

}
