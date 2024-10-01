<?php

declare(strict_types=1);

namespace Drupal\niklan\Plugin\Field\FieldFormatter;

use Drupal\comment\CommentFieldItemList;
use Drupal\comment\CommentInterface;
use Drupal\comment\Plugin\Field\FieldType\CommentItemInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;

#[FieldFormatter(
  id: 'niklan_comment',
  label: new TranslatableMarkup('Improved comment list'),
  field_types: [
    'comment',
  ],
)]
final class CommentFormatter extends FormatterBase {

  public function __construct(
    string $plugin_id,
    array $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    string $label,
    string $view_mode,
    array $third_party_settings,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly AccountInterface $currentUser,
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
  }

  #[\Override]
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get(EntityTypeManagerInterface::class),
      $container->get(AccountInterface::class),
    );
  }

  #[\Override]
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    \assert($items instanceof CommentFieldItemList);

    if (!$this->isVisible($items)) {
      return [];
    }

    $element = [
      '#comment_type' => $this->getFieldSetting('comment_type'),
      '#comment_display_mode' => $this->getFieldSetting('default_mode'),
      'comments' => $this->prepareComments($items),
      // @todo Add form.
      'comment_form' => NULL,
    ];

    return [$element];
  }

  private function isVisible(CommentFieldItemList $items): bool {
    $is_not_hidden = $items->first()->get('status')->getValue() !== CommentItemInterface::HIDDEN;
    $is_user_has_access = $this->currentUser->hasPermission('access comments');

    return $is_not_hidden && $is_user_has_access;
  }

  private function prepareComments(CommentFieldItemList $items): array {
    $comments_data = $this->loadCommentsData($items);

    return $this->buildTree($comments_data, 0);
  }

  private function loadCommentsData(CommentFieldItemList $items): array {
    $commented_entity = $items->getEntity();

    $storage = $this->entityTypeManager->getStorage('comment');
    $thread = $storage->loadThread(
      entity: $commented_entity,
      field_name: $this->fieldDefinition->getName(),
      mode: $items->getSetting('default_mode'),
      comments_per_page: $items->getSetting('per_page'),
    );

    $view_builder = $this->entityTypeManager->getViewBuilder('comment');
    $comments_data = [];

    foreach ($thread as $comment) {
      \assert($comment instanceof CommentInterface);
      $build = $view_builder->view($comment, $this->viewMode);
      // Disable 'indented' wrapper from
      // \Drupal\comment\CommentViewBuilder::alterBuild.
      $build['#comment_threaded'] = FALSE;
      $build['#comment_indent_final'] = FALSE;

      $comments_data[] = [
        'parent_id' => (int) $comment->getParentComment()?->id(),
        'comment_id' => (int) $comment->id(),
        'comment' => $build,
        'comment_thread_depth' => \count(\explode('.', $comment->getThread())),
        'comment_thread' => $comment->getThread(),
      ];
    }

    return $comments_data;
  }

  private function buildTree(array $comments_data, int $parent_id): array {
    $tree = [];

    foreach ($comments_data as $comment_data) {
      if ($comment_data['parent_id'] !== $parent_id) {
        continue;
      }

      $replies = $this->buildTree($comments_data, $comment_data['comment_id']);
      $tree[] = [
        'comment' => $comment_data['comment'],
        'replies' => !$replies ? NULL : [
          '#theme' => 'niklan_comment_thread',
          '#depth' => $comment_data['comment_thread_depth'],
          '#thread_id' => $comment_data['comment_thread'],
          '#comments' => $replies,
        ],
      ];
    }

    return $tree;
  }

}
