<?php declare(strict_types = 1);

namespace Drupal\external_content_test\Source;

use Drupal\external_content\Contract\Source\SourceInterface;
use Drupal\external_content\Data\Data;

/**
 * {@selfdoc}
 */
final readonly class FooSource implements SourceInterface {

  /**
   * Constructs a new FooSource instance.
   */
  public function __construct(
    private string $type,
    private string $contents,
    private Data $data = new Data(),
  ) {}

  /**
   * {@inheritdoc}
   */
  public function data(): Data {
    return $this->data;
  }

  /**
   * {@inheritdoc}
   */
  public function type(): string {
    return $this->type;
  }

  /**
   * {@inheritdoc}
   */
  public function contents(): string {
    return $this->contents;
  }

}
