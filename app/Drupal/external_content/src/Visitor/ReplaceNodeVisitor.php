<?php

declare(strict_types=1);

namespace Drupal\external_content\Visitor;

use Drupal\external_content\Contract\Node\NodeVisitor;
use Drupal\external_content\DataStructure\Nodes\ContentNode;

final readonly class ReplaceNodeVisitor implements NodeVisitor {

  public function __construct(
    private ContentNode $search,
    private ContentNode $replace,
  ) {}

  public function visit(ContentNode $node): void {
    $queue = new \SplQueue();
    $queue->enqueue($node);

    while (!$queue->isEmpty()) {
      $current = $queue->dequeue();
      foreach ($current->getChildren() as &$child) {
        if ($child === $this->search) {
          $this->replace->setParent($current);
          $child = $this->replace;
        }
        else {
          $queue->enqueue($child);
        }
      }
    }
  }

}
