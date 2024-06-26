<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Serializer;

use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
use Drupal\external_content\Contract\Node\NodeInterface;

/**
 * {@selfdoc}
 */
interface ChildSerializerInterface extends EnvironmentAwareInterface {

  /**
   * {@selfdoc}
   */
  public function normalize(NodeInterface $node): array;

  /**
   * {@selfdoc}
   */
  public function deserialize(array $element): NodeInterface;

}
