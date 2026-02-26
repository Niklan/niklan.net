<?php

declare(strict_types=1);

namespace Drupal\app_comment\Comment\Controller;

use Drupal\comment\Controller\CommentController;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Render\Element;
use Symfony\Component\HttpFoundation\Request;

final class CommentReply extends CommentController {

  public function __invoke(Request $request, EntityInterface $entity, string $field_name, ?int $pid = NULL): array {
    $build = parent::getReplyForm($request, $entity, $field_name, $pid);

    $children = [];
    foreach (Element::children($build) as $child) {
      $children[] = $build[$child];
    }

    return [
      '#theme' => 'app_comment_reply_page',
      '#children' => $children,
    ];
  }

}
