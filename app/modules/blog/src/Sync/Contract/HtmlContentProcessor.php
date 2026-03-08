<?php

declare(strict_types=1);

namespace Drupal\app_blog\Sync\Contract;

use Drupal\app_blog\Sync\Domain\ArticleProcessingContext;

interface HtmlContentProcessor {

  public function process(\DOMDocument $dom, ArticleProcessingContext $context): void;

}
