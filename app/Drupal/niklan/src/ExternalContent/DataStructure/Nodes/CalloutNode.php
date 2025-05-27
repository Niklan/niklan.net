<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\DataStructure\Nodes;

use Drupal\external_content\DataStructure\Nodes\ContentNode;

final class CalloutNode extends ContentNode {

  protected array $titleChildren = [];

  public function __construct(
    public string $type,
  ) {}

  public function addTitleChild(ContentNode $node): void {
    $node->setParent($this);
    $this->titleChildren[] = $node;
  }

  /**
   * @return array<\Drupal\external_content\DataStructure\Nodes\ContentNode>
   */
  public function getTitleChildren(): array {
    return $this->titleChildren;
  }

  public function hasTitleChildren(): bool {
    return (bool) \count($this->titleChildren);
  }

  public static function getType(): string {
    return 'niklan:callout';
  }

}
