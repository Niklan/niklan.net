<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\List;

use Drupal\external_content\Domain\ListType;
use Drupal\external_content\Nodes\ContentNode;

final class ListNode extends ContentNode {

  public function __construct(
    ListType $type,
  ) {
    parent::__construct();
    $this->setListType($type);
  }

  public function setListType(ListType $type): void {
    $this->getProperties()->setProperty('type', $type->value);
  }

  public function getListType(): ListType {
    return ListType::from($this->getProperties()->getProperty('type'));
  }

  public static function getType(): string {
    return 'list';
  }

}
