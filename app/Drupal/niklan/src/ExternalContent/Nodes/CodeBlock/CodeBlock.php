<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\CodeBlock;

use Drupal\external_content\DataStructure\HtmlAttributes;
use Drupal\external_content\Nodes\Content\Content;

final class CodeBlock extends Content {

  private HtmlAttributes $attributes;

  public function __construct(
    string $code,
    array $attributes = [],
  ) {
    parent::__construct();
    $this->setCode($code);
    $this->attributes = new HtmlAttributes($attributes);
  }

  public function setCode(string $code): void {
    $this->properties->setProperty('code', $code);
  }

  public function getCode(): string {
    return $this->properties->getProperty('code');
  }

  public function attributes(): HtmlAttributes {
    return $this->attributes;
  }

  public static function getType(): string {
    return 'niklan:code_block';
  }

}
