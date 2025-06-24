<?php

namespace Drupal\external_content\Exporter\Array;

use Drupal\external_content\Utils\Registry;

final readonly class Builder {

  /**
   * @param \Drupal\external_content\Utils\Registry<\Drupal\external_content\Contract\Exporter\ContentArrayElementBuilder> $builders
   */
  public function __construct(
    private Registry $builders,
  ) {}

  public function buildChildren(BuildRequest $build_request): void {
    foreach ($build_request->currentAstNode->getChildren() as $child) {
      $this->buildChild($build_request->withNewAstNode($child));
    }
  }

  private function buildChild(BuildRequest $build_request): void {
    foreach ($this->builders->getAll() as $builder) {
      if (!$builder->supports($build_request)) {
        continue;
      }
      $build_request->currentArrayElement->addChild($builder->build($build_request));
      return;
    }

    $build_request->exportRequest->getContext()->getLogger()->error(
      message: 'No Array Builder found',
      context: ['type' => $build_request->currentAstNode::getType()],
    );
  }

}
