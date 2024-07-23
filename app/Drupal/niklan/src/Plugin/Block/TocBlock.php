<?php

declare(strict_types=1);

namespace Drupal\niklan\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\external_content\Plugin\Field\FieldType\ExternalContentFieldItem;
use Drupal\niklan\Helper\TocBuilder;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a node toc block.
 *
 * @Block(
 *   id = "niklan_node_toc",
 *   admin_label = @Translation("Node TOC"),
 *   category = @Translation("Content")
 * )
 *
 * @todo rework as it done on Druki.
 */
final class TocBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The current route match.
   */
  protected RouteMatchInterface $routeMatch;

  #[\Override]
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    $instance = new self($configuration, $plugin_id, $plugin_definition);
    $instance->routeMatch = $container->get('current_route_match');

    return $instance;
  }

  #[\Override]
  public function build(): array {
    $build = [];
    $node = $this->routeMatch->getParameter('node');

    if (!$node instanceof NodeInterface) {
      return [];
    }

    $cacheable_metadata = new CacheableMetadata();
    $cacheable_metadata->addCacheableDependency($node);

    if ($node->get('external_content')->isEmpty()) {
      return [];
    }

    $content = $node->get('external_content')->first();
    \assert($content instanceof ExternalContentFieldItem);

    $toc_builder = new TocBuilder();
    $links = $toc_builder->getTree($content);

    if (!$links) {
      $cacheable_metadata->applyTo($build);

      return $build;
    }

    $build['content'] = [
      '#theme' => 'niklan_toc',
      '#links' => $links,
    ];

    $cacheable_metadata->applyTo($build);

    return $build;
  }

  #[\Override]
  public function getCacheContexts(): array {
    return Cache::mergeContexts(parent::getCacheContexts(), ['route']);
  }

}
