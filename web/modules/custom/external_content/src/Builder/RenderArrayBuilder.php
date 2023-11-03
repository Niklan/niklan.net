<?php declare(strict_types = 1);

namespace Drupal\external_content\Builder;

use Drupal\external_content\Contract\Builder\BuilderInterface;
use Drupal\external_content\Contract\Builder\BuilderResultInterface;
use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\BuilderResult;

/**
 * Provides an external content render array builder.
 */
final class RenderArrayBuilder implements BuilderInterface, EnvironmentAwareInterface {

  /**
   * The environment.
   */
  protected EnvironmentInterface $environment;

  /**
   * {@inheritdoc}
   */
  public function build(NodeInterface $node, string $type = self::class, array $context = []): BuilderResultInterface {
    $context = $this->buildRecursive($node, $context);
    $build = $this->buildNode($node, $context);
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
   * {@inheritdoc}
   */
  public function supportsBuild(NodeInterface $node, string $type, array $context = []): bool {
    return $type === self::class;
  }

  /**
   * {@inheritdoc}
   */
  public function setEnvironment(EnvironmentInterface $environment): void {
    $this->environment = $environment;
  }

  /**
   * {@selfdoc}
   */
  protected function buildRecursive(NodeInterface $node, array $children): mixed {
    $children_build = [];

    foreach ($node->getChildren() as $child) {
      $children_build[] = $this->buildRecursive($child, $children);
    }

    return $this->buildNode($node, $children_build);
  }

  /**
   * {@selfdoc}
   */
  protected function buildNode(NodeInterface $node, array $children): mixed {
    $context = ['children' => $children];

    foreach ($this->environment->getBuilders() as $builder) {
      \assert($builder instanceof BuilderInterface);

      if (!$builder->supportsBuild($node, self::class, $context)) {
        continue;
      }

      $result = $builder->build($node, self::class, $context);

      if ($result->isNotBuild()) {
        continue;
      }

      return $result->result();
    }

    // If build didn't happen, just return children. Most likely it's a root
    // element.
    return $children;
  }

}
