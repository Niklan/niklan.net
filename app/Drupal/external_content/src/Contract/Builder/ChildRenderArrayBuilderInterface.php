<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Builder;

use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\RenderArrayBuilderResult;

interface ChildRenderArrayBuilderInterface extends EnvironmentAwareInterface {

  public function build(NodeInterface $node): RenderArrayBuilderResult;

}
