<?php

declare(strict_types=1);

namespace Drupal\laszlo\Hook\Theme;

use Drupal\comment\CommentInterface;
use Drupal\niklan\Utils\MediaHelper;

final readonly class PreprocessComment {

  private function addGeneralVariables(CommentInterface $comment, array &$variables): void {
    $variables['author_name'] = $comment->getAuthorName();
    $variables['created_timestamp'] = $comment->getCreatedTime();

    $commented_entity = $comment->getCommentedEntity();
    if ($commented_entity) {
      $variables['anchor'] = "comment-{$comment->id()}";
      $variables['permalink_url'] = $commented_entity->toUrl()->setOption('fragment', $variables['anchor'])->toString();
      $variables['commented_entity_label'] = $commented_entity->label();
    }

    $variables['homepage'] = NULL;
    if ($comment->get('homepage')->isEmpty()) {
      return;
    }
    $variables['homepage'] = $comment->get('homepage')->getString();
  }

  private function addCommentedEntityPosterUri(CommentInterface $comment, array &$variables): void {
    $variables['commented_entity_poster_uri'] = NULL;

    if (!$comment->getCommentedEntity()) {
      return;
    }

    $variables['commented_entity_poster_uri'] = MediaHelper::getFileFromMediaField(
      entity: $comment->getCommentedEntity(),
      field_name: 'field_media_image',
    )?->getFileUri();
  }

  private function addTeaserVariables(CommentInterface $comment, array &$variables): void {
    $this->addCommentedEntityPosterUri($comment, $variables);
  }

  public function __invoke(array &$variables): void {
    $comment = $variables['elements']['#comment'];
    \assert($comment instanceof CommentInterface);
    $this->addGeneralVariables($comment, $variables);

    match ($variables['elements']['#view_mode']) {
      default => NULL,
      'teaser' => $this->addTeaserVariables($comment, $variables),
    };
  }

}
