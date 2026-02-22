<?php

declare(strict_types=1);

namespace Drupal\laszlo\Hook\Theme;

use Drupal\app_contract\Contract\Node\Node;
use Drupal\Core\DependencyInjection\ClassResolverInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\niklan\Node\Entity\ArticleBundle;
use Drupal\niklan\Node\Entity\PortfolioBundle;
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

  private function addCommonVariables(Node $node, array &$variables): void {
    $variables['url_absolute'] = $node->toUrl()->setAbsolute()->toString();
    $variables['published_timestamp'] = $node->getCreatedTime();
  }

  private function addCommentVariables(Node $node, array &$variables): void {
    if (!$node->hasField('comment_node_blog_entry') || $node->get('comment_node_blog_entry')->isEmpty()) {
      return;
    }

    $variables['comment_count'] = $node->get('comment_node_blog_entry')->first()?->get('comment_count')->getValue() ?? 0;
  }

  public function __invoke(array &$variables): void {
    $node = $variables['node'];
    \assert($node instanceof Node);

    $this->addCommonVariables($node, $variables);
    $this->addCommentVariables($node, $variables);

    $class = match ($node::class) {
      default => NULL,
      ArticleBundle::class => PreprocessNodeBlogEntry::class,
      PortfolioBundle::class => PreprocessNodePortfolio::class,
    };

    if (!$class) {
      return;
    }

    $this->classResolver->getInstanceFromDefinition($class)($variables);
  }

}
