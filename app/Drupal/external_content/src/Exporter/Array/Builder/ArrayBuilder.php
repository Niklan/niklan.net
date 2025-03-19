<?php

namespace Drupal\external_content\Exporter\Array\Builder;

use Drupal\external_content\Contract\Exporter\ArrayElementBuilder;
use Drupal\external_content\Utils\PrioritizedList;

final class ArrayBuilder {

  /**
   * @var \Drupal\external_content\Utils\PrioritizedList<\Drupal\external_content\Contract\Exporter\ArrayElementBuilder>
   */
  private PrioritizedList $parsers;

  public function __construct() {
    $this->parsers = new PrioritizedList();
  }

  public function addBuilder(ArrayElementBuilder $builder, int $priority = 0): void {
    $this->parsers->add($builder, $priority);
  }

  public function buildChildren(ArrayBuildRequest $build_request): void {
    foreach ($build_request->currentAstNode->getChildren() as $child) {
      $this->buildChild($build_request->withNewAstNode($child));
    }
  }

  private function buildChild(ArrayBuildRequest $build_request): void {
    foreach ($this->parsers as $parser) {
      if (!$parser->supports($build_request)) {
        continue;
      }

      $build_request->currentArrayElement->addChild($parser->build($build_request));

      return;
    }

    $build_request->exportRequest->getContext()->getLogger()->error("No Array builder found for node type: {$build_request->currentAstNode->getType()}");
  }

}
