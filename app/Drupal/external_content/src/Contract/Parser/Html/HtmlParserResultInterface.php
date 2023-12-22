<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Parser\Html;

use Drupal\external_content\Contract\Node\NodeInterface;

/**
 * Represents a result for HTML parser result.
 */
interface HtmlParserResultInterface {

  /**
   * Checks for replacement element.
   */
  public function hasReplacement(): bool;

  /**
   * Gets the replacement.
   */
  public function getReplacement(): ?NodeInterface;

  /**
   * Checks should parse continue or not.
   */
  public function shouldContinue(): bool;

  /**
   * Checks for should parse stop or not.
   */
  public function shouldNotContinue(): bool;

}
