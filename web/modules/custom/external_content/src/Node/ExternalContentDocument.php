<?php declare(strict_types = 1);

namespace Drupal\external_content\Node;

use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\ExternalContentFile;

/**
 * Provides an external content document.
 */
final class ExternalContentDocument extends Node {

  /**
   * Constructs a new ExternalContentDocument instance.
   *
   * @param \Drupal\external_content\Data\ExternalContentFile $file
   *   The external content file.
   */
  public function __construct(
    protected ExternalContentFile $file,
  ) {}

  /**
   * Gets the file.
   */
  public function getFile(): ExternalContentFile {
    return $this->file;
  }

  /**
   * {@inheritdoc}
   */
  public function hasParent(): bool {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function setParent(NodeInterface $node): NodeInterface {
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getParent(): ?NodeInterface {
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getRoot(): NodeInterface {
    return $this;
  }

}
