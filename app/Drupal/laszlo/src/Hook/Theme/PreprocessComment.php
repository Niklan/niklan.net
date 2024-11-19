<?php

declare(strict_types=1);

namespace Drupal\laszlo\Hook\Theme;

use Drupal\comment\CommentInterface;
use Drupal\file\FileInterface;
use Drupal\media\MediaInterface;

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
    $media = $comment->getCommentedEntity()->get('field_media_image')->first()?->get('entity')->getValue();
    \assert(\is_null($media) || $media instanceof MediaInterface);
    $file_field = $media?->getSource()->getConfiguration()['source_field'];
    $file = $media?->get($file_field)->first()?->get('entity')->getValue();
    \assert(\is_null($file) || $file instanceof FileInterface);
    $variables['commented_entity_poster_uri'] = $file?->getFileUri();
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
