<?php

declare(strict_types=1);

namespace Drupal\app_blog\Hook\Deploy;

use Drupal\Core\Database\Connection;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Sets blog_entry body format to 'blog_article' where it is NULL.
 */
final class Deploy0002 implements ContainerInjectionInterface {

  public function __construct(
    private Connection $database,
  ) {}

  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('database'),
    );
  }

  public function __invoke(): string {
    $updated = $this->database
      ->update('node__body')
      ->fields(['body_format' => 'blog_article'])
      ->condition('bundle', 'blog_entry')
      ->isNull('body_format')
      ->execute();

    return \sprintf('Updated format for %d blog_entry body fields.', $updated);
  }

}
