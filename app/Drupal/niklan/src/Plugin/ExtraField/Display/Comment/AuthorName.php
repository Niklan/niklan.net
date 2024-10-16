<?php

declare(strict_types=1);

namespace Drupal\niklan\Plugin\ExtraField\Display\Comment;

use Drupal\comment\CommentInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\extra_field\Plugin\ExtraFieldDisplayBase;

/**
 * @ExtraFieldDisplay(
 *   id = "author_name",
 *   label = @Translation("Author name"),
 *   bundles = {
 *     "comment.*",
 *   }
 * )
 *
 * @deprecated Remove after new theme is deployed.
 */
final class AuthorName extends ExtraFieldDisplayBase {

  #[\Override]
  public function view(ContentEntityInterface $entity): array {
    \assert($entity instanceof CommentInterface);

    return [
      '#markup' => $entity->getAuthorName(),
    ];
  }

}
