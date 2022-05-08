<?php

declare(strict_types=1);

namespace Drupal\niklan\Element;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\Element\RenderElement;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Drupal\node\NodeStorageInterface;
use Drupal\node\NodeViewBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides render element to display last blog posts.
 *
 * @RenderElement("niklan_last_blog_posts")
 */
final class LastBlogPosts extends RenderElement implements ContainerFactoryPluginInterface {

  /**
   * The node storage.
   */
  protected NodeStorageInterface $nodeStorage;

  /**
   * The node view builder.
   */
  protected NodeViewBuilder $nodeViewBuilder;

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
    // Do not render element if not blog posts found.
    if (empty($items)) {
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
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function prepareResults(): array {
    $results = [];
    $ids = $this->findResults();
    if (empty($ids)) {
      return $results;
    }

    $entities = $this->getNodeStorage()->loadMultiple($ids);
    foreach ($entities as $entity) {
      $results[] = $this->getNodeViewBuilder()->view($entity, $this->viewMode);
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
    $query = $this->getNodeStorage()->getQuery()->accessCheck(FALSE);
    $query->condition('type', 'blog_entry')
      ->condition('status', NodeInterface::PUBLISHED)
      ->sort('created', 'DESC')
      ->range(0, $this->limit);

    return $query->execute();
  }

  /**
   * Gets node storage.
   *
   * @return \Drupal\node\NodeStorageInterface
   *   The node storage.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getNodeStorage(): NodeStorageInterface {
    if (!isset($this->nodeStorage)) {
      $this->nodeStorage = $this->entityTypeManager->getStorage('node');
    }
    return $this->nodeStorage;
  }

  /**
   * Gets node view builder.
   *
   * @return \Drupal\node\NodeViewBuilder
   *   The node view builder.
   */
  protected function getNodeViewBuilder(): NodeViewBuilder {
    if (!isset($this->nodeViewBuilder)) {
      $this->nodeViewBuilder = $this->entityTypeManager->getViewBuilder('node');
    }
    return $this->nodeViewBuilder;
  }

}
