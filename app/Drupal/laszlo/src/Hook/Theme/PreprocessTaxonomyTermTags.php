<?php

declare(strict_types=1);

namespace Drupal\laszlo\Hook\Theme;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class PreprocessTaxonomyTermTags implements ContainerInjectionInterface {

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self();
  }

  public function __invoke(array &$variables): void {
    dump($variables);
    // @todo Statistics from \Drupal\niklan\Plugin\ExtraField\Display\Term\Tag\Statistics
    // Refactor it into utility service.
  }

}