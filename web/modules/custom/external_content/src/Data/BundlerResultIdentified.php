<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

use Drupal\external_content\Contract\Bundler\BundlerResultIdentifiedInterface;

/**
 * Provides a value object for identified bundler result.
 */
final class BundlerResultIdentified extends BundlerResult implements BundlerResultIdentifiedInterface {

  /**
   * Constructs a new BundlerResultIdentified instance.
   *
   * @param string $id
   *   The document identifier.
   * @param \Drupal\external_content\Data\Attributes $attributes
   *   The document attributes.
   */
  public function __construct(
    protected string $id,
    protected Attributes $attributes,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function isIdentified(): bool {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function isUnidentified(): bool {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function id(): string {
    return $this->id;
  }

  /**
   * {@inheritdoc}
   */
  public function attributes(): Attributes {
    return $this->attributes;
  }

}
