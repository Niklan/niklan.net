<?php declare(strict_types = 1);

namespace Drupal\external_content\Builder;

use Drupal\Component\Utility\SortArray;
use Drupal\external_content\Dto\ElementInterface;
use Drupal\external_content\Plugin\ExternalContent\Builder\BuilderPluginManagerInterface;

/**
 * Provides a chained render array builder.
 */
final class ChainRenderArrayBuilder implements ChainRenderArrayBuilderInterface {

  /**
   * An array with available builders.
   *
   * @var \Drupal\external_content\Plugin\ExternalContent\Builder\BuilderInterface[]
   */
  protected array $builders = [];

  /**
   * Constructs a new ChainRenderArrayBuilder object.
   *
   * @param \Drupal\external_content\Plugin\ExternalContent\Builder\BuilderPluginManagerInterface $builderPluginManager
   *   The builder plugin manager.
   */
  public function __construct(
    protected readonly BuilderPluginManagerInterface $builderPluginManager,
  ) {}

  /**
   * Tries to build a single element render array.
   *
   * @param \Drupal\external_content\Dto\ElementInterface $element
   *   The element to build.
   *
   * @return array
   *   The result render array.
   */
  protected function doBuildElement(ElementInterface $element): array {
    // The default value if build is not happened.
    $build = [];
    /** @var \Drupal\external_content\Plugin\ExternalContent\Builder\BuilderInterface $builder */
    foreach ($this->builders as $builder) {
      if ($builder::isApplicable($element)) {
        $build = $builder->build($element);
        break;
      }
    }

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function build(ElementInterface $element): array {
    $this->initBuilders();

    return $this->buildElement($element);
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
   * Builds a single element and taking into account it's children.
   *
   * @param \Drupal\external_content\Dto\ElementInterface $element
   *   The element to build.
   *
   * @return array
   *   The result render array.
   */
  protected function buildElement(ElementInterface $element): array {
    $children = [];
    if ($element->hasChildren()) {
      foreach ($element->getChildren() as $child) {
        $children[] = $this->buildElement($child);
      }
    }

    $build = $this->doBuildElement($element);

    return \array_merge($build, $children);
  }

}
