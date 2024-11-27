<?php

declare(strict_types=1);

namespace Drupal\niklan\Comment\Controller;

use Drupal\comment\Controller\CommentController;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Render\Element;
use Symfony\Component\HttpFoundation\Request;

final class CommentReply extends CommentController {

  public function __invoke(Request $request, EntityInterface $entity, $field_name, $pid = NULL): array {
    $build = parent::getReplyForm($request, $entity, $field_name, $pid);

    $children = [];
    foreach (Element::children($build) as $child) {
      $children[] = $build[$child];
    }

    return [
      '#theme' => 'niklan_comment_reply_page',
      '#children' => $children,
    ];
  }

}
