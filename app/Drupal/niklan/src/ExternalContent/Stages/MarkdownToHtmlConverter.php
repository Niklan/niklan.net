<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Stages;

use Drupal\external_content\Contract\Pipeline\PipelineContext;
use Drupal\external_content\Contract\Pipeline\PipelineStage;
use Drupal\niklan\ExternalContent\Domain\ArticleTranslationProcessContext;
use Drupal\niklan\Utils\PathHelper;
use League\CommonMark\MarkdownConverter;

final readonly class MarkdownToHtmlConverter implements PipelineStage {

  public function __construct(
    private MarkdownConverter $converter,
  ) {}

  #[\Override]
  public function process(PipelineContext $context): void {
    \assert($context instanceof ArticleTranslationProcessContext);
    \dump(PathHelper::normalizePath($context->article->directory . '/' . $context->article->getPrimaryTranslation()->sourcePath));
  }

}
