<?php declare(strict_types = 1);

namespace Drupal\external_content_test\Parser\Html;

use Drupal\external_content\Contract\Parser\Html\HtmlParserInterface;
use Drupal\external_content\Contract\Parser\Html\HtmlParserResultInterface;
use Drupal\external_content\Data\HtmlParserResult;

/**
 * {@selfdoc}
 */
final class ContinueParser implements HtmlParserInterface {

  /**
   * {@inheritdoc}
   */
  public function parseNode(\DOMNode $node): HtmlParserResultInterface {
    return HtmlParserResult::continue();
  }

}
