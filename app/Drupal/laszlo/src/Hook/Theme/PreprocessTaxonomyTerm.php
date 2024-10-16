<?php

declare(strict_types=1);

namespace Drupal\laszlo\Hook\Theme;

use Drupal\Core\DependencyInjection\ClassResolverInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\taxonomy\TermInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class PreprocessTaxonomyTerm implements ContainerInjectionInterface {

  public function __construct(
    private ClassResolverInterface $classResolver,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(ClassResolverInterface::class),
    );
  }

  public function __invoke(array &$variables): void {
    $term = $variables['term'];
    \assert($term instanceof TermInterface);

    $class = match ($term->bundle()) {
      default => NULL,
      'tags' => PreprocessTaxonomyTermTags::class,
    };

    if (!$class) {
      return;
    }

    $this->classResolver->getInstanceFromDefinition($class)($variables);
  }

}
