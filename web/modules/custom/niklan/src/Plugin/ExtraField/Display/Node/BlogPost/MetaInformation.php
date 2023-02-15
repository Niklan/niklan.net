<?php declare(strict_types = 1);

namespace Drupal\niklan\Plugin\ExtraField\Display\Node\BlogPost;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Drupal\extra_field\Plugin\ExtraFieldDisplayBase;
use Drupal\niklan\Helper\EstimatedReadTimeCalculator;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Blog meta information.
 *
 * @ExtraFieldDisplay(
 *   id = "meta_information",
 *   label = @Translation("Meta information"),
 *   bundles = {
 *     "node.blog_entry",
 *   }
 * )
 */
final class MetaInformation extends ExtraFieldDisplayBase implements ContainerFactoryPluginInterface {

  /**
   * The date formatter.
   */
  protected DateFormatterInterface $dateFormatter;

  /**
   * The data cache bin.
   */
  protected CacheBackendInterface $cache;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    $instance = new self($configuration, $plugin_id, $plugin_definition);
    $instance->dateFormatter = $container->get('date.formatter');
    $instance->cache = $container->get('cache.data');

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function view(ContentEntityInterface $entity): array {
    \assert($entity instanceof NodeInterface);

    return [
      '#theme' => 'niklan_blog_meta',
      '#created' => $this->getCreatedDate($entity),
      '#comment_count' => $this->getCommentCount(),
      '#comments_url' => $this->getCommentsUrl(),
      '#estimated_read_time' => $this->getEstimatedReadTime(),
    ];
  }

  /**
   * Builds created element.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node entity.
   *
   * @return string
   *   The created date.
   */
  protected function getCreatedDate(NodeInterface $node): string {
    return $this->dateFormatter->format($node->getCreatedTime(), 'dmy');
  }

  /**
   * Gets comment count.
   *
   * @return int
   *   The number of comments added.
   */
  protected function getCommentCount(): int {
    return (int) $this->getEntity()
      ->get('comment_node_blog_entry')
      ->first()
      ->get('comment_count')
      ->getValue();
  }

  /**
   * Gets comments URL.
   *
   * @return \Drupal\Core\Url
   *   The URL to comments.
   */
  protected function getCommentsUrl(): Url {
    return $this->getEntity()->toUrl('canonical', ['fragment' => 'comments']);
  }

  /**
   * Gets estimated read time in minutes.
   *
   * @return int
   *   The number of minutes.
   */
  protected function getEstimatedReadTime(): int {
    $cid = __METHOD__ . ':' . $this->getEntity()->id();
    $cache = $this->cache->get($cid);

    if ($cache) {
      $estimated_minutes = $cache->data;
    }
    else {
      $calculator = new EstimatedReadTimeCalculator();
      $estimated_minutes = $calculator
        ->calculate($this->getEntity()->get('field_content'));
      $this->cache->set($cid, $estimated_minutes);
    }

    return $estimated_minutes;
  }

}
