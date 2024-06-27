<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Builder;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\RenderArrayBuilderResult;

/**
 * {@selfdoc}
 */
interface RenderArrayBuilderManagerInterface {

  /**
   * {@selfdoc}
   */
  public function build(NodeInterface $node, EnvironmentInterface $environment): RenderArrayBuilderResult;

  /**
   * {@selfdoc}
   */
  public function get(string $builder_id): RenderArrayBuilderInterface;

  /**
   * {@selfdoc}
   */
  public function has(string $builder_id): bool;

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
