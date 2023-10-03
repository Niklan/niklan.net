<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Builder;

use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
use Drupal\external_content\Contract\Node\NodeInterface;

/**
 * Represents an external content render array builder.
 */
interface RenderArrayBuilderFacadeInterface extends EnvironmentAwareInterface {

  /**
   * Builds a render array from document.
   */
  public function build(NodeInterface $document): BuilderResultRenderArrayInterface;

}
