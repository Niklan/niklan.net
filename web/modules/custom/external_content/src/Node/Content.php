<?php declare(strict_types = 1);

namespace Drupal\external_content\Node;

use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Contract\Source\SourceInterface;
use Drupal\external_content\Data\Data;

/**
 * Provides an external content document.
 */
final class Content extends Node {

  /**
   * Constructs a new Content instance.
   */
  public function __construct(
    protected SourceInterface $source,
    protected ?Data $data = NULL,
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
   * Gets the content source.
   */
  public function getSource(): SourceInterface {
    return $this->source;
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
