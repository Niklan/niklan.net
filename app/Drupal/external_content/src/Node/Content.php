<?php

declare(strict_types=1);

namespace Drupal\external_content\Node;

use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\Data;

/**
 * Provides an external content document.
 */
final class Content extends Node {

  /**
   * Constructs a new Content instance.
   */
  public function __construct(
    protected ?Data $data = NULL,
  ) {
    $this->data ??= new Data();
  }

  public function getData(): Data {
    return $this->data;
  }

  #[\Override]
  public function hasParent(): bool {
    return FALSE;
  }

  #[\Override]
  public function setParent(NodeInterface $node): NodeInterface {
    return $this;
  }

  #[\Override]
  public function getParent(): ?NodeInterface {
    return NULL;
  }

  #[\Override]
  public function getRoot(): NodeInterface {
    return $this;
  }

}
