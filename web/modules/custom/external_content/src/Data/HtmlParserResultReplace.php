<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

use Drupal\external_content\Contract\Node\NodeInterface;

/**
 * Provides a loader result that continues processing without explicit result.
 */
final class HtmlParserResultReplace extends HtmlParserResult {

  /**
   * Constructs a new HtmlParserResultReplace instance.
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
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function shouldNotContinue(): bool {
    return FALSE;
  }

}
