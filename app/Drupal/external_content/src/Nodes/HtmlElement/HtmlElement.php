<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\HtmlElement;

use Drupal\external_content\DataStructure\HtmlAttributes;
use Drupal\external_content\Nodes\Content\Content;

final class HtmlElement extends Content {

  private HtmlAttributes $attributes;

  public function __construct(
    string $tag,
    array $attributes = [],
  ) {
    parent::__construct();
    $this->setTag($tag);
    $this->attributes = new HtmlAttributes($attributes);
  }

  public function setTag(string $tag): void {
    $this->getProperties()->setProperty('tag', $tag);
  }

  public function getTag(): string {
    return $this->getProperties()->getProperty('tag');
  }

  public function attributes(): HtmlAttributes {
    return $this->attributes;
  }

  public static function getType(): string {
    return 'html_element';
  }

}
