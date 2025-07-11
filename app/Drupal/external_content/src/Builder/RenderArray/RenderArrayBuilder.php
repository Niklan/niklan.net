<?php

namespace Drupal\external_content\Builder\RenderArray;

use Drupal\external_content\Contract\Builder\RenderArray\Builder;
use Drupal\external_content\Contract\Builder\RenderArray\ChildBuilder;
use Drupal\external_content\DataStructure\RenderArray;
use Drupal\external_content\Nodes\Document;
use Drupal\external_content\Nodes\Node;
use Drupal\external_content\Utils\Registry;
use Psr\Log\LoggerInterface;

final readonly class RenderArrayBuilder implements Builder, ChildBuilder {

  /**
   * @param \Drupal\external_content\Utils\Registry<\Drupal\external_content\Contract\Builder\RenderArray\Builder> $builders
   */
  public function __construct(
    private Registry $builders,
    private LoggerInterface $logger,
  ) {}

  public function build(Document $document): RenderArray {
    $root = new RenderArray();
    $this->buildChildren($document, $root);
    return $root;
  }

  public function buildChildren(Node $parent_node, RenderArray $render_array): void {
    foreach ($parent_node->getChildren() as $child) {
      $render_array = $this->buildElement($child, $this);
      if (!$render_array) {
        continue;
      }
      $render_array->addChild($render_array);
    }
  }

  public function buildElement(Node $node, ChildBuilder $child_builder): ?RenderArray {
    foreach ($this->builders->getAll() as $builder) {
      if (!$builder->supports($node)) {
        continue;
      }

      $array = $builder->buildElement($node);
      if (!$array) {
        continue;
      }

      $child_builder->buildChildren($node, $array);
      return $array;
    }

    $this->logger->warning('Missing render array builder', [
      'type' => $node::getNodeType(),
    ]);
    return NULL;
  }

  public function supports(Node $node): bool {
    return $node instanceof Document;
  }

}
