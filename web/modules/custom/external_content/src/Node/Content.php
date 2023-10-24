<?php declare(strict_types = 1);

namespace Drupal\external_content\Node;

use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\Data;
use Drupal\external_content\Source\File;

/**
 * Provides an external content document.
 */
final class Content extends Node {

  /**
   * Constructs a new Content instance.
   */
  public function __construct(
    protected File $file,
    protected Data $data,
  ) {
    $this->data ??= new Data();
  }

  /**
   * {@selfdoc}
   */
  public function getData(): Data {
    return $this->data;
  }

  /**
   * Gets the file.
   */
  public function getFile(): File {
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
