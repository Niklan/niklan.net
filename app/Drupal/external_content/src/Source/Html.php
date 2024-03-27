<?php declare(strict_types = 1);

namespace Drupal\external_content\Source;

use Drupal\external_content\Contract\Source\SourceInterface;
use Drupal\external_content\Data\Data;

/**
 * {@selfdoc}
 */
final readonly class Html implements SourceInterface {

  /**
   * {@selfdoc}
   */
  public function __construct(
    private string $contents,
    private ?Data $data = new Data(),
  ) {}

  /**
   * {@selfdoc}
   */
  #[\Override]
  public function data(): Data {
    return $this->data;
  }

  /**
   * {@selfdoc}
   */
  #[\Override]
  public function type(): string {
    return 'text/html';
  }

  /**
   * {@selfdoc}
   */
  #[\Override]
  public function contents(): string {
    return $this->contents;
  }

}
