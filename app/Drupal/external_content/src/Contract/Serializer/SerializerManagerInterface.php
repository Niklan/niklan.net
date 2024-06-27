<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Serializer;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Node\NodeInterface;

/**
 * Defines the interface for an External Content DOM serializer.
 */
interface SerializerManagerInterface {

  /**
   * {@selfdoc}
   */
  public function normalize(NodeInterface $node, EnvironmentInterface $environment): string;

  /**
   * {@selfdoc}
   */
  public function deserialize(string $json, EnvironmentInterface $environment): NodeInterface;

  /**
   * {@selfdoc}
   */
  public function get(string $serializer_id): SerializerInterface;

  /**
   * {@selfdoc}
   */
  public function has(string $serializer_id): bool;

  /**
   * {@selfdoc}
   *
   * @return array{
   *   service: string,
   *   id: string,
   *   }
   */
  public function list(): array;

}
