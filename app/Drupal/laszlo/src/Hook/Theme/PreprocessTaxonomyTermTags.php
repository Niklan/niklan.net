<?php

declare(strict_types=1);

namespace Drupal\laszlo\Hook\Theme;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\niklan\Tag\Contract\TagUsageStatistics;
use Drupal\taxonomy\TermInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class PreprocessTaxonomyTermTags implements ContainerInjectionInterface {

  public function __construct(
    private TagUsageStatistics $statistics,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(TagUsageStatistics::class),
    );
  }

  public function __invoke(array &$variables): void {
    $term = $variables['term'];
    \assert($term instanceof TermInterface);

    $variables['publications_count'] = $this
      ->statistics
      ->count((int) $term->id());
    $variables['first_publication_date'] = $this
      ->statistics
      ->firstPublicationDate((int) $term->id());
    $variables['last_publication_date'] = $this
      ->statistics
      ->lastPublicationDate((int) $term->id());
  }

}
