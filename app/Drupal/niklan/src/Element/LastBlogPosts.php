<?php

declare(strict_types=1);

namespace Drupal\niklan\Element;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\Element\RenderElement;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides render element to display last blog posts.
 *
 * @RenderElement("niklan_last_blog_posts")
 */
final class LastBlogPosts extends RenderElement implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The amount of blog posts to load.
   */
  protected int $limit;

  /**
   * The view mode used to render items.
   */
  protected string $viewMode;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    $instance = new self($configuration, $plugin_id, $plugin_definition);
    $instance->entityTypeManager = $container->get('entity_type.manager');

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
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

  /**
   * Prepare element for rendering.
   *
   * @param array $element
   *   An array with element.
   *
   * @return array
   *   An array with modifier element.
   */
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

  /**
   * Builds items array.
   *
   * @return array
   *   An array with items to render. Empty if nothing found.
   */
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

  /**
   * Find last blog post IDs.
   *
   * @return array
   *   An array with last blog post IDs.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
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
