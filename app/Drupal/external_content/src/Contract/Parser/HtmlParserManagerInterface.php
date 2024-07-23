<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Parser;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Node\Content;
use Drupal\external_content\Source\Html;

interface HtmlParserManagerInterface {

  public function parse(Html $html, EnvironmentInterface $environment): Content;

  public function get(string $parser_id): HtmlParserInterface;

  public function has(string $parser_id): bool;

  /**
   * @return array{
   *   service: string,
   *   id: string,
   *   }
   */
  public function list(): array;

}
