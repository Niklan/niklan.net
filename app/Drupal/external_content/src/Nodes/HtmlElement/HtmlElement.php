<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\HtmlElement;

use Drupal\external_content\Nodes\Content\Content;

final class HtmlElement extends Content {

  public function __construct(
    string $tag,
  ) {
    parent::__construct();
    $this->setTag($tag);
  }

  public function setTag(string $tag): void {
    $this->getProperties()->setProperty('tag', $tag);
  }

  public function getTag(): string {
    return $this->getProperties()->getProperty('tag');
  }

  public static function getType(): string {
    return 'html_element';
  }

}
