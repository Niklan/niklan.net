<?php

declare(strict_types=1);

namespace Drupal\niklan\Controller;

use Symfony\Component\HttpFoundation\Request;

/**
 * Defines an interface for search controller.
 */
interface SearchControllerInterface {

  /**
   * Builds a search page.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   *
   * @return array
   *   An array with page content.
   */
  public function page(Request $request): array;

  /**
   * Builds a search page content.
   *
   * @param string|null $keys
   *   The search keys.
   *
   * @return array
   *   The page content.
   */
  public function buildPageContent(?string $keys): array;

  /**
   * Builds a page title.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   */
  public function pageTitle(Request $request): string;

}
