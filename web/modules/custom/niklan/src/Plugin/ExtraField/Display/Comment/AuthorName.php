<?php

declare(strict_types = 1);

namespace Drupal\niklan\Plugin\ExtraField\Display\Comment;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\extra_field\Plugin\ExtraFieldDisplayBase;

/**
 * The author name.
 *
 * @ExtraFieldDisplay(
 *   id = "author_name",
 *   label = @Translation("Author name"),
 *   bundles = {
 *     "comment.*",
 *   }
 * )
 */
final class AuthorName extends ExtraFieldDisplayBase {

  /**
   * {@inheritdoc}
   */
  public function view(ContentEntityInterface $entity): array {
    return [
      '#markup' => $entity->getAuthorName(),
    ];
  }

}
