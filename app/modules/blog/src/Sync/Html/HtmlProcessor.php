<?php

declare(strict_types=1);

namespace Drupal\app_blog\Sync\Html;

use Drupal\app_blog\Sync\Contract\HtmlContentProcessor;
use Drupal\app_blog\Sync\Domain\ArticleProcessingContext;
use Drupal\Component\Utility\Html;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final readonly class HtmlProcessor {

  /**
   * @param iterable<\Drupal\app_blog\Sync\Contract\HtmlContentProcessor> $processors
   */
  public function __construct(
    #[AutowireIterator(HtmlContentProcessor::class)]
    private iterable $processors,
  ) {}

  public function process(string $html, ArticleProcessingContext $context): string {
    $dom = Html::load($html);

    foreach ($this->processors as $processor) {
      $processor->process($dom, $context);
    }

    return Html::serialize($dom);
  }

}
