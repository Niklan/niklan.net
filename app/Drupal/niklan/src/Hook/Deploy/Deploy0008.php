<?php

declare(strict_types=1);

namespace Drupal\niklan\Hook\Deploy;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\taxonomy\TermInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class Deploy0008 implements ContainerInjectionInterface {

  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(EntityTypeManagerInterface::class),
    );
  }

  public function __invoke(array &$sandbox): string {
    $mapping = [
      27 => 'ubuntu',
      28 => 'linux',
      29 => 'web-server',
      30 => 'drupal',
      34 => 'drupal-7',
      55 => 'drupal-8',
      113 => 'drupal-9',
      57 => 'ide',
      32 => 'windows',
      111 => 'i18n',
      84 => 'seo',
      40 => 'drush',
      50 => 'javascript',
      52 => 'performance',
      62 => 'search',
      63 => 'e-commerce',
      67 => 'theming',
      68 => 'access',
      100 => 'twig',
      107 => 'docker',
    ];
    $ids = \array_keys($mapping);
    $storage = $this->entityTypeManager->getStorage('taxonomy_term');
    foreach ($storage->loadMultiple($ids) as $term) {
      \assert($term instanceof TermInterface);
      $term->set('external_id', $mapping[(int) $term->id()]);
      $term->save();
    }
    return 'All categories have been updated.';
  }

}
