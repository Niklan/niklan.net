<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Parser;

/**
 * @template TSource of \Drupal\external_content\Contract\Parser\ParserSource
 * @template TContext of \Drupal\external_content\Contract\Parser\ParserContext
 */
interface ParseRequest {

  public function getSource(): ParserSource;

  public function getContext(): ParserContext;

}
