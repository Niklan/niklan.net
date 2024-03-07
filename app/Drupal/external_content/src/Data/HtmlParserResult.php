<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Contract\Parser\Html\HtmlParserResultInterface;

/**
 * Represents an HTML parser status.
 */
final class HtmlParserResult {

  public static function replaceAndContinue(NodeInterface $node): self {
    return new HtmlParserResultReplace($node);
  }

  public static function replaceAndStop(NodeInterface $node): self {
    return new HtmlParserResultFinalize($node);
  }

  public static function pass(): self {
    return new HtmlParserResultContinue();
  }

  public static function stop(): self {
    return new HtmlParserResultStop();
  }

}
