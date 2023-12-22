<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Contract\Parser\Html\HtmlParserResultInterface;

/**
 * Represents an HTML parser status.
 */
abstract class HtmlParserResult implements HtmlParserResultInterface {

  /**
   * Replaces parsed element and continue parsing children.
   */
  public static function replace(NodeInterface $node): self {
    return new HtmlParserResultReplace($node);
  }

  /**
   * Replaces parsed element and stop parsing its children.
   */
  public static function finalize(NodeInterface $node): self {
    return new HtmlParserResultFinalize($node);
  }

  /**
   * Indicates that parsing of element should continue.
   */
  public static function continue(): self {
    return new HtmlParserResultContinue();
  }

  /**
   * Indicates that parsing of element should be stopped without replacement.
   */
  public static function stop(): self {
    return new HtmlParserResultStop();
  }

}
