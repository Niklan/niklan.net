<?php

declare(strict_types = 1);

namespace Drupal\external_content\Parser;

use Drupal\external_content\Dto\ElementInterface;
use Drupal\external_content\Dto\HtmlParserStateInterface;
use Drupal\external_content\Dto\SourceFileContent;

/**
 * Provides an interface for chain HTML parser.
 *
 * HTML parser is responsible to parse HTML markup into typed DTOs.
 */
interface ChainHtmlParserInterface {

  /**
   * Parse root HTML element.
   *
   * @param string $html
   *   The whole content HTML.
   * @param \Drupal\external_content\Dto\HtmlParserStateInterface $html_parser_state
   *   The parser state.
   *
   * @return \Drupal\external_content\Dto\SourceFileContent
   *   The parsed source file content.
   */
  public function parseRoot(string $html, HtmlParserStateInterface $html_parser_state): SourceFileContent;

  /**
   * Parse a single DOM element.
   *
   * @param \DOMNode $node
   *   The dom element to parse.
   * @param \Drupal\external_content\Dto\HtmlParserStateInterface $html_parser_state
   *   The current HTML parser state.
   *
   * @return \Drupal\external_content\Dto\ElementInterface|null
   *   The result element.
   */
  public function parseElement(\DOMNode $node, HtmlParserStateInterface $html_parser_state): ?ElementInterface;

}
