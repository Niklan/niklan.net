<?php

declare(strict_types=1);

namespace Drupal\app_blog\ExternalContent\Nodes\Callout;

use Drupal\external_content\Nodes\Node;
use Drupal\app_blog\ExternalContent\Nodes\CalloutBody\CalloutBody;
use Drupal\app_blog\ExternalContent\Nodes\CalloutTitle\CalloutTitle;

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
    return \array_find($this->children, static fn (Node $child): bool => $child instanceof CalloutBody);
  }

  public function getTitle(): ?CalloutTitle {
    return \array_find($this->children, static fn (Node $child): bool => $child instanceof CalloutTitle);
  }

  public static function getNodeType(): string {
    return 'niklan:callout';
  }

}
