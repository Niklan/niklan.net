<?php declare(strict_types = 1);

namespace Drupal\external_content\Parser;

use Drupal\Component\FrontMatter\FrontMatter;
use Drupal\external_content\Contract\ChainHtmlParserInterface;
use Drupal\external_content\Converter\ChainMarkupConverter;
use Drupal\external_content\Data\HtmlParserState;
use Drupal\external_content\Data\ParsedSourceFile;
use Drupal\external_content\Data\SourceFile;
use Drupal\external_content\Data\SourceFileParams;

/**
 * Component for parsing source file contents.
 *
 * This component is intended to parse SourceFile contents. It will extract
 * FrontMatter and content from its source and store in separate value objects.
 */
final class SourceFileParser {

  /**
   * Constructs a new SourceFileParser object.
   *
   * @param \Drupal\external_content\Converter\ChainMarkupConverter $chainMarkupConverter
   *   The chain markup converter.
   * @param \Drupal\external_content\Contract\ChainHtmlParserInterface $chainHtmlParser
   *   The chain HTML parser.
   */
  public function __construct(
    protected ChainMarkupConverter $chainMarkupConverter,
    protected ChainHtmlParserInterface $chainHtmlParser,
  ) {}

  /**
   * Parse source file contents.
   *
   * @param \Drupal\external_content\Data\SourceFile $source_file
   *   The source file.
   *
   * @return \Drupal\external_content\Data\ParsedSourceFile
   *   The parsed source file.
   */
  public function parse(SourceFile $source_file): ParsedSourceFile {
    $front_matter = FrontMatter::create($source_file->getContents());
    $params = new SourceFileParams($front_matter->getData());
    $html = $this->chainMarkupConverter->convert(
      $source_file->getExtension(),
      $front_matter->getContent(),
    );
    $html_parser_state = new HtmlParserState(
      $source_file,
      $params,
      $this->chainHtmlParser,
    );
    $content = $this->chainHtmlParser->parseRoot($html, $html_parser_state);

    return new ParsedSourceFile($source_file, $params, $content);
  }

}
