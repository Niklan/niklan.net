<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

use Drupal\external_content\Contract\Node\NodeInterface;

/**
 * Provides a loader result that finishes processing without any result.
 */
final class HtmlParserResultStop extends HtmlParserResult {

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
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function shouldNotContinue(): bool {
    return TRUE;
  }

}
