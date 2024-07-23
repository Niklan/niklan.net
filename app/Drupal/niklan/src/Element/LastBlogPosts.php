<?php

declare(strict_types=1);

namespace Drupal\niklan\Element;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\Attribute\RenderElement;
use Drupal\Core\Render\Element\RenderElementBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @todo Consider remove that element.
 */
#[RenderElement('niklan_last_blog_posts')]
final class LastBlogPosts extends RenderElementBase implements ContainerFactoryPluginInterface {

  protected EntityTypeManagerInterface $entityTypeManager;
  protected int $limit;
  protected string $viewMode;

  #[\Override]
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    $instance = new self($configuration, $plugin_id, $plugin_definition);
    $instance->entityTypeManager = $container->get('entity_type.manager');

    return $instance;
  }

  #[\Override]
  public function getInfo(): array {
    return [
      '#theme' => 'niklan_last_content',
      '#limit' => 3,
      '#view_mode' => 'teaser',
      '#title' => new TranslatableMarkup('Last blog posts'),
      '#more_url' => Url::fromRoute('niklan.blog_list'),
      '#more_label' => new TranslatableMarkup('All posts'),
      '#pre_render' => [
        [$this, 'preRenderElement'],
      ],
    ];
  }

  public function preRenderElement(array $element): array {
    $this->limit = $element['#limit'];
    $this->viewMode = $element['#view_mode'];

    $items = $this->prepareResults();

    // Do not render element if no blog posts found.
    if (!$items) {
      return [];
    }

    $element['#items'] = $items;

    return $element;
  }

  protected function prepareResults(): array {
    $results = [];
    $ids = $this->findResults();

    if (!$ids) {
      return $results;
    }

    $storage = $this->entityTypeManager->getStorage('node');
    $view_builder = $this->entityTypeManager->getViewBuilder('node');
    $entities = $storage->loadMultiple($ids);

    foreach ($entities as $entity) {
      $results[] = $view_builder->view($entity, $this->viewMode);
    }

    return $results;
  }

  protected function findResults(): array {
    $query = $this
      ->entityTypeManager
      ->getStorage('node')
      ->getQuery()
      ->accessCheck(FALSE);

    $query
      ->condition('type', 'blog_entry')
      ->condition('status', NodeInterface::PUBLISHED)
      ->sort('created', 'DESC')
      ->range(0, $this->limit);

    return $query->execute();
  }

}
