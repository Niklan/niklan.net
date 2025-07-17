<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\Callout;

use Drupal\external_content\Nodes\Node;
use Drupal\niklan\ExternalContent\Nodes\CalloutBody\CalloutBody;
use Drupal\niklan\ExternalContent\Nodes\CalloutTitle\CalloutTitle;

final class Callout extends Node {

  public function __construct(
    public readonly string $type,
  ) {}

  public function addChild(Node $node): void {
    if (!$node instanceof CalloutTitle && !$node instanceof CalloutBody) {
      throw new \InvalidArgumentException(\sprintf('
        Only %s and %s can be added as children, %s given.',
        CalloutTitle::class,
        CalloutBody::class,
        $node::class,
      ));
    }
    parent::addChild($node);
  }

  public function getBody(): ?CalloutBody {
    foreach ($this->children as $child) {
      if ($child instanceof CalloutBody) {
        return $child;
      }
    }
    return NULL;
  }

  public function getTitle(): ?CalloutTitle {
    foreach ($this->children as $child) {
      if ($child instanceof CalloutTitle) {
        return $child;
      }
    }

    return NULL;
  }

  public static function getNodeType(): string {
    return 'niklan:callout';
  }

}
