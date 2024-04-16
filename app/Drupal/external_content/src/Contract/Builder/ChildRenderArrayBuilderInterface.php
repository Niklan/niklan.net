<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Builder;

use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
use Drupal\external_content\Data\RenderArrayBuilderResult;

/**
 * {@selfdoc}
 */
interface ChildRenderArrayBuilderInterface extends EnvironmentAwareInterface {

  /**
   * {@selfdoc}
   */
  public function build(\DOMNode $node): RenderArrayBuilderResult;

}
