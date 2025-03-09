<?php

declare(strict_types=1);

namespace Drupal\external_content\Node;

use Drupal\external_content\Domain\ListType;

final class ListNode extends ElementNode {

  public function __construct(
    ListType $type,
  ) {
    parent::__construct('list');
    $this->setListType($type);
  }

  public function setListType(ListType $type): void {
    $this->getProperties()->setProperty('type', $type->value);
  }

  public function getListType(): ListType {
    return ListType::from($this->getProperties()->getProperty('type'));
  }

  /**
   * @throws \InvalidArgumentException
   */
  #[\Override]
  public function addChild(ContentNode $node): void {
    if (!$node instanceof ListItemNode) {
      throw new \InvalidArgumentException('List node can only accept list items');
    }
    parent::addChild($node);
  }

}
