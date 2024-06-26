<?php declare(strict_types = 1);

namespace Drupal\niklan\Node\ExternalContent;

use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Node\Node;

/**
 * {@selfdoc}
 *
 * @ingroup content_sync
 */
final class Alert extends Node {

  /**
   * {@selfdoc}
   */
  public function __construct(
    public readonly string $type,
    public ?NodeInterface $heading,
  ) {
    $this->heading?->setParent($this);
  }

  /**
   * {@inheritdoc}
   */
  public function replaceNode(NodeInterface $search, NodeInterface $replace): self {
    parent::replaceNode($search, $replace);

    if ($this->heading === $search) {
      $this->heading = $replace;
    }

    return $this;
  }

}
