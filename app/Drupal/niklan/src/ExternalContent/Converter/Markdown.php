<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Converter;

use Drupal\Component\FrontMatter\FrontMatter;
use Drupal\external_content\Contract\Converter\ConverterInterface;
use Drupal\external_content\Contract\Source\SourceInterface;
use Drupal\external_content\Data\ConverterResult;
use Drupal\external_content\Source\Html;
use League\CommonMark\MarkdownConverter as LeagueMarkdownConverter;

/**
 * @ingroup external_content
 */
final readonly class Markdown implements ConverterInterface {

  public function __construct(
    private LeagueMarkdownConverter $converter,
  ) {}

  #[\Override]
  public function convert(SourceInterface $input): ConverterResult {
    if ($input->type() !== 'text/markdown') {
      return ConverterResult::pass();
    }

    // Remove Front Matter from the source.
    $front_matter = FrontMatter::create($input->contents());
    $result = $this->converter->convert($front_matter->getContent());
    $html = new Html($result->getContent(), $input->data());

    return ConverterResult::withHtml($html);
  }

}
