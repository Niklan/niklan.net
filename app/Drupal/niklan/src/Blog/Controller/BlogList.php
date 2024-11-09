<?php

declare(strict_types=1);

namespace Drupal\niklan\Blog\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class BlogList implements ContainerInjectionInterface {

  protected int $limit = 10;

  public function __construct(
    protected EntityTypeManagerInterface $entityTypeManager,
    protected LanguageManagerInterface $languageManager,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(EntityTypeManagerInterface::class),
      $container->get(LanguageManagerInterface::class),
    );
  }

  /**
   * Builds list of entities.
   *
   * @return array
   *   An array with blog post render arrays.
   */
  protected function getItems(): array {
    $items = [];

    foreach ($this->load() as $node) {
      $items[] = $this
        ->entityTypeManager
        ->getViewBuilder('node')
        ->view($node, 'teaser');
    }

    return $items;
  }

  /**
   * Builds pager element.
   *
   * @return array
   *   The render array with pager.
   */
  protected function buildPager(): array {
    return [
      '#type' => 'pager',
      '#quantity' => 4,
    ];
  }

  /**
   * Loads entities.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   An array with blog articles.
   */
  protected function load(): array {
    $ids = $this->getEntityIds();

    return $this->entityTypeManager->getStorage('node')->loadMultiple($ids);
  }

  /**
   * Gets entity ids.
   *
   * @return array
   *   The list of ids.
   */
  protected function getEntityIds(): array {
    $query = $this
      ->entityTypeManager
      ->getStorage('node')
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', 'blog_entry')
      ->condition('status', NodeInterface::PUBLISHED)
      ->condition('langcode', $this->languageManager->getCurrentLanguage()->getId(), '=')
      ->sort('created', 'DESC');

    if ($this->limit) {
      $query->pager($this->limit);
    }

    return $query->execute();
  }

  public function __invoke(): array {
    return [
      '#theme' => 'niklan_blog_list',
      '#items' => $this->getItems(),
      '#pager' => $this->buildPager(),
      '#cache' => [
        'tags' => ['node_list:blog_entry'],
      ],
    ];
  }

}
