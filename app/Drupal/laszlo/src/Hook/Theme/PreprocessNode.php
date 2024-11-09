<?php

declare(strict_types=1);

namespace Drupal\laszlo\Hook\Theme;

use Drupal\Core\DependencyInjection\ClassResolverInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\niklan\Content\Blog\Entity\BlogEntry;
use Drupal\niklan\Content\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class PreprocessNode implements ContainerInjectionInterface {

  public function __construct(
    private ClassResolverInterface $classResolver,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(ClassResolverInterface::class),
    );
  }

  private function addCommonVariables(NodeInterface $node, array &$variables): void {
    $variables['url_absolute'] = $node->toUrl()->setAbsolute()->toString();
    $variables['published_timestamp'] = $node->getCreatedTime();
    $variables['comment_count'] = $node
      ->get('comment_node_blog_entry')
      ->first()
      ->get('comment_count')
      ->getValue();
  }

  public function __invoke(array &$variables): void {
    $node = $variables['node'];
    \assert($node instanceof NodeInterface);

    $this->addCommonVariables($node, $variables);

    $class = match ($node::class) {
      default => NULL,
      BlogEntry::class => PreprocessNodeBlogEntry::class,
    };

    if (!$class) {
      return;
    }

    $this->classResolver->getInstanceFromDefinition($class)($variables);
  }

}
