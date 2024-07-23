<?php

declare(strict_types=1);

namespace Drupal\niklan\Plugin\ExtraField\Display\Term\Tag;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\PluralTranslatableMarkup;
use Drupal\extra_field\Plugin\ExtraFieldDisplayBase;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Share links.
 *
 * @ExtraFieldDisplay(
 *   id = "niklan_taxonomy_tag_statistics",
 *   label = @Translation("Tag statistics"),
 *   bundles = {
 *     "taxonomy_term.tags",
 *   }
 * )
 */
final class Statistics extends ExtraFieldDisplayBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  #[\Override]
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    $instance = new self($configuration, $plugin_id, $plugin_definition);
    $instance->entityTypeManager = $container->get('entity_type.manager');

    return $instance;
  }

  #[\Override]
  public function view(ContentEntityInterface $entity): array {
    $articles = $this->findArticles();

    if (!$articles) {
      return [];
    }

    $first_article = $this->loadFirstArticle($articles);
    $last_article = $this->loadLastArticle($articles);
    $dates = $this->buildDateRange($first_article, $last_article);

    return [
      '#markup' => $this->buildStatisticsSummary(\count($articles), $dates),
    ];
  }

  /**
   * Finds articles for that tag.
   */
  protected function findArticles(): array {
    return $this
      ->entityTypeManager
      ->getStorage('node')
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', 'blog_entry')
      ->condition('status', NodeInterface::PUBLISHED)
      ->condition('field_tags', $this->getEntity()->id())
      ->sort('created', 'ASC')
      ->execute();
  }

  /**
   * Loads first article from the list of found.
   *
   * @param array $articles
   *   The array with found articles.
   */
  protected function loadFirstArticle(array $articles): NodeInterface {
    $id = \array_shift($articles);
    $node = $this->entityTypeManager->getStorage('node')->load($id);
    \assert($node instanceof NodeInterface);

    return $node;
  }

  /**
   * Loads last article from the list of found.
   *
   * @param array $articles
   *   The array with found articles.
   */
  protected function loadLastArticle(array $articles): NodeInterface {
    $id = \array_pop($articles);
    $node = $this->entityTypeManager->getStorage('node')->load($id);
    \assert($node instanceof NodeInterface);

    return $node;
  }

  /**
   * Builds date range string for provided articles.
   *
   * @param \Drupal\node\NodeInterface $first_article
   *   The first published article.
   * @param \Drupal\node\NodeInterface $last_article
   *   The last published article.
   *
   * @return array
   *   An array with first and last created dates.
   */
  protected function buildDateRange(NodeInterface $first_article, NodeInterface $last_article): array {
    $first_created = DrupalDateTime::createFromTimestamp(
      $first_article->getCreatedTime(),
    );
    $last_created = DrupalDateTime::createFromTimestamp(
      $last_article->getCreatedTime(),
    );

    return [
      $first_created->format('j F Y'),
      $last_created->format('j F Y'),
    ];
  }

  /**
   * Builds summary for statistics.
   *
   * @param int $count
   *   The amount of published articles.
   * @param array $date_range
   *   The date ranges.
   */
  protected function buildStatisticsSummary(int $count, array $date_range): string {
    $without_range = (string) new PluralTranslatableMarkup(
      $count,
      '@count publication from @date',
      '@count publications from @date',
      ['@date' => $date_range[0]],
    );

    $with_range = (string) new PluralTranslatableMarkup(
      $count,
      '@count publication from @date_start to @date_end',
      '@count publications from @date_start to @date_end',
      ['@date_start' => $date_range[0], '@date_end' => $date_range[1]],
    );

    return $date_range[0] === $date_range[1] ? $without_range : $with_range;
  }

}
