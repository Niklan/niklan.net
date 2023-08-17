<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

use Drupal\external_content\Contract\Node\NodeInterface;

/**
 * Provides a loader result that finishes processing with a replacement.
 */
final class HtmlParserResultFinalize extends HtmlParserResult {

  /**
   * Constructs a new HtmlParserResultFinalize instance.
   */
  public function __construct(
    protected NodeInterface $replacement,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function hasReplacement(): bool {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getReplacement(): ?NodeInterface {
    return $this->replacement;
  }

  /**
   * {@inheritdoc}
   */
  public function shouldContinue(): bool {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function shouldNotContinue(): bool {
    return TRUE;
  }

}
