<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Builder;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\RenderArrayBuilderResult;

interface RenderArrayBuilderManagerInterface {

  public function build(NodeInterface $node, EnvironmentInterface $environment): RenderArrayBuilderResult;

  public function get(string $builder_id): RenderArrayBuilderInterface;

  public function has(string $builder_id): bool;

  /**
   * @return array<string, array{service: string, id: string}>
   */
  public function list(): array;

}
