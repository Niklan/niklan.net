<?php

declare(strict_types=1);

namespace Drupal\niklan\Controller;

use Symfony\Component\HttpFoundation\Request;

interface SearchControllerInterface {

  public function page(Request $request): array;

  public function buildPageContent(?string $keys): array;

  public function pageTitle(Request $request): string;

}
