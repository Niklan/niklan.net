<?php

declare(strict_types=1);

namespace Drupal\niklan\StaticPage\Home\Controller;

use Drupal\Core\Database\Connection;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityViewBuilderInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class Home implements ContainerInjectionInterface {

  private const int LIMIT_PREVIEW_POSTS = 6;

  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
    private LanguageManagerInterface $languageManager,
    private Connection $connection,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(EntityTypeManagerInterface::class),
      $container->get(LanguageManagerInterface::class),
      $container->get(Connection::class),
    );
  }

  private function getCurrentLanguageId(): string {
    return $this->languageManager->getCurrentLanguage()->getId();
  }

  private function getNodeStorage(): EntityStorageInterface {
    return $this->entityTypeManager->getStorage('node');
  }

  private function getNodeViewBuilder(): EntityViewBuilderInterface {
    return $this->entityTypeManager->getViewBuilder('node');
  }

  private function addLatestPosts(array &$build): void {
    $ids = $this
      ->getNodeStorage()
      ->getQuery()
      ->accessCheck(FALSE)
      ->range(0, self::LIMIT_PREVIEW_POSTS)
      ->condition('type', 'blog_entry')
      ->condition('status', '1')
      ->condition('langcode', $this->getCurrentLanguageId())
      ->sort('created', 'DESC')->execute();

    if (!$ids) {
      return;
    }

    $build['#sections']['latest_posts'] = [
      '#heading' => new TranslatableMarkup('Latest posts'),
      '#theme' => 'niklan_blog_preview_list',
      '#items' => \array_map(
        callback: fn ($node) => $this->getNodeViewBuilder()->view($node, 'preview'),
        array: $this->getNodeStorage()->loadMultiple($ids),
      ),
    ];
  }

  private function addTooBigToReadPosts(array &$build): void {
    $query = $this
      ->connection
      ->select('node_field_data', 'nfd')
      ->fields('nfd', ['nid'])
      ->condition('nfd.type', 'blog_entry')
      ->condition('nfd.langcode', $this->getCurrentLanguageId())
      ->condition('nfd.status', '1')
      ->range(0, self::LIMIT_PREVIEW_POSTS);
    $query->leftJoin('node__external_content', 'ec', '[nfd].[nid] = [ec].[entity_id]');
    $query->addExpression('CHAR_LENGTH([ec].[external_content_value])', 'length');
    $ids = $query
      ->orderBy('length', 'DESC')
      ->execute()
      ->fetchCol();

    if (!$ids) {
      return;
    }

    $build['#sections']['too_big_to_read'] = [
      '#heading' => new TranslatableMarkup("TL;DR - ”I'd rather read a book”"),
      '#theme' => 'niklan_blog_preview_list',
      '#items' => \array_map(
        callback: fn ($node) => $this->getNodeViewBuilder()->view($node, 'preview'),
        array: $this->getNodeStorage()->loadMultiple($ids),
      ),
    ];
  }

  private function addMostDiscussed(array &$build): void {
    $query = $this
      ->connection
      ->select('node_field_data', 'nfd')
      ->fields('nfd', ['nid'])
      ->condition('nfd.type', 'blog_entry')
      ->condition('nfd.langcode', $this->getCurrentLanguageId())
      ->condition('nfd.status', '1')
      ->range(0, self::LIMIT_PREVIEW_POSTS);
    $query->leftJoin('comment_entity_statistics', 'ces', '[nfd].[nid] = [ces].[entity_id] AND [ces].[entity_type] = :type', [
      ':type' => 'node',
    ]);
    $ids = $query
      ->orderBy('ces.comment_count', 'DESC')
      ->execute()
      ->fetchCol();

    if (!$ids) {
      return;
    }

    $build['#sections']['most_discussed'] = [
      '#heading' => new TranslatableMarkup("The most discussed"),
      '#theme' => 'niklan_blog_preview_list',
      '#items' => \array_map(
        callback: fn (EntityInterface $node) => $this->getNodeViewBuilder()->view($node, 'preview'),
        array: $this->getNodeStorage()->loadMultiple($ids),
      ),
    ];
  }

  private function addLatestComments(array &$build): void {
    $storage = $this->entityTypeManager->getStorage('comment');
    $ids = $storage
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('status', '1')
      ->condition('langcode', $this->getCurrentLanguageId())
      ->sort('created', 'DESC')
      ->range(0, 10)
      ->execute();

    if (!$ids) {
      return;
    }

    $build['#sections']['latest_comments'] = [
      '#heading' => new TranslatableMarkup('Opinion of anonymous users from the Internet'),
      '#theme' => 'niklan_comment_list',
      '#items' => \array_map(
        callback: fn (EntityInterface $comment) => $this->entityTypeManager->getViewBuilder('comment')->view($comment, 'teaser'),
        array: $storage->loadMultiple($ids),
      ),
    ];
  }

  public function __invoke(): array {
    $build = [
      '#theme' => 'niklan_home',
      '#sections' => [],
    ];

    $this->addLatestPosts($build);
    $this->addTooBigToReadPosts($build);
    $this->addMostDiscussed($build);
    $this->addLatestComments($build);

    return $build;
  }

}
