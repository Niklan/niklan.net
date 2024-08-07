<?php

declare(strict_types=1);

namespace Drupal\external_content\Builder;

use Drupal\external_content\Contract\Builder\ChildRenderArrayBuilderInterface;
use Drupal\external_content\Contract\Builder\RenderArrayBuilderInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\RenderArrayBuilderResult;

final class ChildRenderArrayBuilder implements ChildRenderArrayBuilderInterface {

  private EnvironmentInterface $environment;

  #[\Override]
  public function build(NodeInterface $node): RenderArrayBuilderResult {
    $build = [];

    foreach ($this->environment->getRenderArrayBuilders() as $builder) {
      \assert($builder instanceof RenderArrayBuilderInterface);

      if (!$builder->supportsBuild($node)) {
        continue;
      }

      $result = $builder->build($node, $this);

      if ($result->isNotBuild()) {
        continue;
      }

      $build = $result->result();

      break;
    }

    return RenderArrayBuilderResult::withRenderArray($build);
  }

  #[\Override]
  public function setEnvironment(EnvironmentInterface $environment): void {
    $this->environment = $environment;
  }

}
