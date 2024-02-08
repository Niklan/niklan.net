<?php declare(strict_types = 1);

namespace Drupal\niklan\Converter;

use Drupal\niklan\Builder\BlogMarkdownEnvironmentBuilder;
use League\CommonMark\MarkdownConverter;

/**
 * {@selfdoc}
 */
final readonly class BlogMarkdownConverter {

  /**
   * Constructs a new BlogMarkdownConverter instance.
   */
  public function __construct(
    private BlogMarkdownEnvironmentBuilder $markdownEnvironmentBuilder,
  ) {}

  /**
   * {@selfdoc}
   */
  public function convert(string $markdown): string {
    $environment = $this->markdownEnvironmentBuilder->build();
    $converter = new MarkdownConverter($environment);

    return $converter->convert($markdown)->getContent();
  }

}
