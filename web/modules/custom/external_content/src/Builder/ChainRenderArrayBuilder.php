<?php declare(strict_types = 1);

namespace Drupal\external_content\Builder;

use Drupal\Component\Utility\SortArray;
use Drupal\external_content\Contract\BuilderPluginInterface;
use Drupal\external_content\Contract\BuilderPluginManagerInterface;
use Drupal\external_content\Contract\ChainRenderArrayBuilderInterface;
use Drupal\external_content\Contract\ElementInterface;

/**
 * Provides a chained render array builder.
 */
final class ChainRenderArrayBuilder implements ChainRenderArrayBuilderInterface {

  /**
   * An array with available builders.
   *
   * @var \Drupal\external_content\Contract\BuilderPluginInterface[]
   */
  protected array $builders = [];

  /**
   * Constructs a new ChainRenderArrayBuilder object.
   *
   * @param \Drupal\external_content\Contract\BuilderPluginManagerInterface $builderPluginManager
   *   The builder plugin manager.
   */
  public function __construct(
    protected readonly BuilderPluginManagerInterface $builderPluginManager,
  ) {}

  /**
   * Tries to build a single element render array.
   *
   * @param \Drupal\external_content\Contract\ElementInterface $element
   *   The element to build.
   * @param array $children
   *   An array with children built elements.
   *
   * @return array
   *   The result render array.
   */
  protected function doBuild(ElementInterface $element, array $children): array {
    foreach ($this->builders as $builder) {
      \assert($builder instanceof BuilderPluginInterface);

      if ($builder::isApplicable($element)) {
        return $builder->build($element, $children);
      }
    }

    // If build didn't happen, just return children. Most likely it's a root
    // collection like SourceFileContent.
    return $children;
  }

  /**
   * {@inheritdoc}
   */
  public function build(ElementInterface $element): array {
    $this->initBuilders();

    $children = $this->buildRecursive($element, []);

    return $this->doBuild($element, $children);
  }

  /**
   * Instantiates builder plugins.
   */
  protected function initBuilders(): void {
    if (\count($this->builders)) {
      return;
    }

    $definitions = $this->builderPluginManager->getDefinitions();
    \uasort($definitions, [SortArray::class, 'sortByWeightElement']);

    foreach (\array_keys($definitions) as $builder_id) {
      $this->builders[$builder_id] = $this
        ->builderPluginManager
        ->createInstance($builder_id);
    }
  }

  /**
   * Builds a single element and taking into account its children.
   *
   * @param \Drupal\external_content\Contract\ElementInterface $element
   *   The element to build.
   * @param array $children
   *   An array with children built elements.
   *
   * @return array
   *   The result render array.
   */
  protected function buildRecursive(ElementInterface $element, array $children): array {
    $children_build = [];

    foreach ($element->getChildren() as $child) {
      $children_build[] = $this->buildRecursive($child, $children);
    }

    return $this->doBuild($element, $children_build);
  }

}
