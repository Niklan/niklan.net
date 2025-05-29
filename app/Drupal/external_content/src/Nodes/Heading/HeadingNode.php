<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Heading;

use Drupal\external_content\Domain\HeadingTagType;
use Drupal\external_content\Nodes\ContentNode;

final class HeadingNode extends ContentNode {

  public function __construct(
    HeadingTagType $tag,
  ) {
    parent::__construct();
    $this->setTag($tag);
  }

  public function getTag(): HeadingTagType {
    return HeadingTagType::from($this->getProperties()->getProperty('tag'));
  }

  public function setTag(HeadingTagType $tag): void {
    $this->getProperties()->setProperty('tag', $tag->value);
  }

  public static function getType(): string {
    return 'heading';
  }

}
