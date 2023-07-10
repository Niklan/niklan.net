<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

use Drupal\external_content\Contract\Loader\LoaderResultEntityInterface;

/**
 * Provides a successful loader result with Drupal entity as destination.
 */
final class LoaderResultEntity extends LoaderResult implements LoaderResultEntityInterface {

  /**
   * Constructs a new LoaderResultEntity instance.
   */
  public function __construct(
    protected string $entityTypeId,
    protected string $entityId,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function getEntityTypeId(): string {
    return $this->entityTypeId;
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityId(): string {
    return $this->entityId;
  }

  /**
   * {@inheritdoc}
   */
  public function isSuccess(): bool {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function isNotSuccess(): bool {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function shouldContinue(): bool {
    return FALSE;
  }

}
