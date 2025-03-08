<?php

declare(strict_types=1);

namespace Drupal\external_content\Node;

use Drupal\external_content\Domain\HeadingTagType;

final class HeadingNode extends ElementNode {

  public function __construct(
    HeadingTagType $tag,
  ) {
    parent::__construct('heading');
    $this->setTag($tag);
  }

  public function getTag(): HeadingTagType {
    return HeadingTagType::from($this->getProperty('tag'));
  }

  public function setTag(HeadingTagType $tag): void {
    $this->setProperty('tag', $tag->value);
  }

}
