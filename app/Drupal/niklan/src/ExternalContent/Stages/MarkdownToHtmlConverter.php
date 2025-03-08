<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Stages;

use Drupal\external_content\Contract\Pipeline\Config;
use Drupal\external_content\Contract\Pipeline\Context;
use Drupal\external_content\Contract\Pipeline\Stage;
use Drupal\niklan\ExternalContent\Domain\BlogArticleProcessContext;
use League\CommonMark\MarkdownConverter;

final readonly class MarkdownToHtmlConverter implements Stage {

  public function __construct(
    private MarkdownConverter $converter,
  ) {}

  #[\Override]
  public function process(Context $context, Config $config): Context {
    \assert($context instanceof BlogArticleProcessContext);

    return $context;
  }

}
