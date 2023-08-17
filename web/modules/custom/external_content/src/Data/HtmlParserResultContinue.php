<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

use Drupal\external_content\Contract\Node\NodeInterface;

/**
 * Provides a loader result that continues processing without explicit result.
 */
final class HtmlParserResultContinue extends HtmlParserResult {

  /**
   * {@inheritdoc}
   */
  public function hasReplacement(): bool {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getReplacement(): ?NodeInterface {
    return NULL;
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
