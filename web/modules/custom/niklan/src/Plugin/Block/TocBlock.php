<?php declare(strict_types = 1);

namespace Drupal\niklan\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\entity_reference_revisions\EntityReferenceRevisionsFieldItemList;
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

  /**
   * The renderer.
   */
  protected RendererInterface $renderer;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    $instance = new self($configuration, $plugin_id, $plugin_definition);
    $instance->routeMatch = $container->get('current_route_match');
    $instance->renderer = $container->get('renderer');

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $build = [];
    $node = $this->routeMatch->getParameter('node');

    if (!$node instanceof NodeInterface) {
      return [];
    }

    $this->renderer->addCacheableDependency($build, $node);

    $content = $node->get('field_content');
    \assert($content instanceof EntityReferenceRevisionsFieldItemList);

    $toc_builder = new TocBuilder();
    $links = $toc_builder->getTree($content);

    if (!$links) {
      return $build;
    }

    $build['content'] = [
      '#theme' => 'niklan_toc',
      '#links' => $links,
    ];

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts(): array {
    return Cache::mergeContexts(parent::getCacheContexts(), ['url.path']);
  }

}
