<?php

declare(strict_types=1);

namespace Drupal\niklan\Controller;

use Drupal\taxonomy\TermInterface;

interface TagControllerInterface {

  public function collection(): array;

  public function page(TermInterface $term): array;

}
