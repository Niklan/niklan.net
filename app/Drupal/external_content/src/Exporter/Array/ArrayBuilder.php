<?php

namespace Drupal\external_content\Exporter\Array;

use Drupal\external_content\Utils\Registry;

final readonly class ArrayBuilder {

  /**
   * @param \Drupal\external_content\Utils\Registry<\Drupal\external_content\Exporter\Array\Builder\ArrayElementBuilder> $builders
   */
  public function __construct(
    private Registry $builders,
  ) {}

  public function buildChildren(ArrayBuildRequest $build_request): void {
    foreach ($build_request->currentAstNode->getChildren() as $child) {
      $this->buildChild($build_request->withNewAstNode($child));
    }
  }

  private function buildChild(ArrayBuildRequest $build_request): void {
    foreach ($this->builders->getAll() as $builder) {
      if (!$builder->supports($build_request)) {
        continue;
      }

      $build_request->currentArrayElement->addChild($builder->build($build_request));

      return;
    }

    $build_request->exportRequest->getContext()->getLogger()->error("No Array builder found for node type: {$build_request->currentAstNode::getType()}");
  }

}
