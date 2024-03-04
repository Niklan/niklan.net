<?php declare(strict_types = 1);

namespace Drupal\niklan\Converter\ExternalContent;

use Drupal\external_content\Contract\Converter\ConverterInterface;
use Drupal\external_content\Contract\Source\SourceInterface;
use Drupal\external_content\Data\ConverterResult;
use Drupal\external_content\Source\Html;
use League\CommonMark\MarkdownConverter as LeagueMarkdownConverter;

/**
 * {@selfdoc}
 *
 * @ingroup external_content
 */
final readonly class MarkdownConverter implements ConverterInterface {

  /**
   * {@selfdoc}
   */
  public function __construct(
    private LeagueMarkdownConverter $converter,
  ) {}

  /**
   * {@selfdoc}
   */
  #[\Override]
  public function convert(SourceInterface $input): ConverterResult {
    if ($input->type() !== 'text/markdown') {
      return ConverterResult::pass();
    }

    $result = $this->converter->convert($input->contents());
    $html = new Html($result->getContent(), $input->data());

    return ConverterResult::withHtml($html);
  }

}
