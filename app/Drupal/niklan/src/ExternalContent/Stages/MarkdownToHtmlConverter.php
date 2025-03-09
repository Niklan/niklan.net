<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Stages;

use Drupal\external_content\Contract\Pipeline\PipelineConfig;
use Drupal\external_content\Contract\Pipeline\PipelineContext;
use Drupal\external_content\Contract\Pipeline\PipelineStage;
use Drupal\niklan\ExternalContent\Domain\BlogArticleProcessPipelineContext;
use League\CommonMark\MarkdownConverter;

final readonly class MarkdownToHtmlConverter implements PipelineStage {

  public function __construct(
    private MarkdownConverter $converter,
  ) {}

  #[\Override]
  public function process(PipelineContext $context, PipelineConfig $config): void {
    \assert($context instanceof BlogArticleProcessPipelineContext);
  }

}
