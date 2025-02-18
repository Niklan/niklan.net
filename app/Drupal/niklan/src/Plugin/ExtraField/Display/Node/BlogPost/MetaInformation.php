<?php

declare(strict_types=1);

namespace Drupal\niklan\Plugin\ExtraField\Display\Node\BlogPost;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Drupal\external_content\Contract\Node\NodeInterface as ContentNodeInterface;
use Drupal\extra_field\Plugin\ExtraFieldDisplayBase;
use Drupal\niklan\ExternalContent\Utils\EstimatedReadTimeCalculator;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @ExtraFieldDisplay(
 *   id = "meta_information",
 *   label = @Translation("Meta information"),
 *   bundles = {
 *     "node.blog_entry",
 *   }
 * )
 *
 * @deprecated Remove after a new theme is deployed
 */
final class MetaInformation extends ExtraFieldDisplayBase implements ContainerFactoryPluginInterface {

  protected DateFormatterInterface $dateFormatter;

  #[\Override]
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    $instance = new self($configuration, $plugin_id, $plugin_definition);
    $instance->dateFormatter = $container->get('date.formatter');

    return $instance;
  }

  #[\Override]
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

  protected function getCreatedDate(NodeInterface $node): string {
    return $this->dateFormatter->format($node->getCreatedTime(), 'dmy');
  }

  protected function getCommentCount(): int {
    $count = $this->getEntity()->get('comment_node_blog_entry')->first()?->get('comment_count')->getValue();
    \assert(\is_int($count) || \is_null($count));

    return $count ?? 0;
  }

  protected function getCommentsUrl(): Url {
    return $this->getEntity()->toUrl('canonical', ['fragment' => 'comments']);
  }

  protected function getEstimatedReadTime(): int {
    $content = $this
      ->getEntity()
      ->get('external_content')
      ->first()
      ?->get('content')
        ->getValue();

    if (!$content instanceof ContentNodeInterface) {
      return 0;
    }

    $calculator = new EstimatedReadTimeCalculator();

    return $calculator->calculate($content);
  }

}
