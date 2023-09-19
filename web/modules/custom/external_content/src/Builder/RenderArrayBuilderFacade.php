<?php declare(strict_types = 1);

namespace Drupal\external_content\Builder;

use Drupal\external_content\Contract\Builder\BuilderInterface;
use Drupal\external_content\Contract\Builder\BuilderResultRenderArrayInterface;
use Drupal\external_content\Contract\Builder\RenderArrayBuilderFacadeInterface;
use Drupal\external_content\Contract\DependencyInjection\EnvironmentAwareClassResolverInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\BuilderResult;
use Drupal\external_content\Node\ExternalContentDocument;

/**
 * Provides an external content render array builder.
 */
final class RenderArrayBuilderFacade implements RenderArrayBuilderFacadeInterface {

  /**
   * The environment.
   */
  protected EnvironmentInterface $environment;

  /**
   * Constructs a new RenderArrayBuilderFacade instance.
   *
   * @param \Drupal\external_content\Contract\DependencyInjection\EnvironmentAwareClassResolverInterface $classResolver
   *   The class resolver.
   */
  public function __construct(
    protected EnvironmentAwareClassResolverInterface $classResolver,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function build(ExternalContentDocument $document): BuilderResultRenderArrayInterface {
    $children = $this->buildRecursive($document, []);
    $build = $this->buildNode($document, $children);
    // Since this is root build, it will always have only one array element.
    // This doesn't make any sense, so it is reset to an actual children build.
    //
    // E.g. before:
    // @code
    // $build = [
    //   0 => [
    //     0 => ['#markup' => 'foo'],
    //     1 => ['#markup' => 'bar'],
    //   ],
    // ];
    // @endcode
    //
    // By doing reset we flatten it to:
    // @code
    // $build = [
    //   0 => ['#markup' => 'foo'],
    //   1 => ['#markup' => 'bar'],
    // ];
    // @endcode
    $build = \reset($build);

    return BuilderResult::renderArray($build);
  }

  /**
   * Builds an element with its children.
   */
  protected function buildRecursive(NodeInterface $node, array $children): array {
    $children_build = [];

    foreach ($node->getChildren() as $child) {
      $children_build[] = $this->buildRecursive($child, $children);
    }

    return $this->buildNode($node, $children_build);
  }

  /**
   * Builds a single node.
   */
  protected function buildNode(NodeInterface $node, array $children): array {
    foreach ($this->environment->getBuilders() as $builder) {
      $instance = $this->classResolver->getInstance(
        $builder,
        BuilderInterface::class,
        $this->getEnvironment(),
      );
      \assert($instance instanceof BuilderInterface);
      $result = $instance->build($node, $children);

      if ($result->isNotBuild()) {
        continue;
      }

      \assert($result instanceof BuilderResultRenderArrayInterface);

      return $result->getRenderArray();
    }

    // If build didn't happen, just return children. Most likely it's a root
    // collection like SourceFileContent.
    return $children;
  }

  /**
   * {@inheritdoc}
   */
  public function getEnvironment(): EnvironmentInterface {
    return $this->environment;
  }

  /**
   * {@inheritdoc}
   */
  public function setEnvironment(EnvironmentInterface $environment): void {
    $this->environment = $environment;
  }

}
