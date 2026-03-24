<?php

declare(strict_types=1);

namespace Drupal\app_portfolio\Hook\Deploy;

use Drupal\app_portfolio\Repository\PortfolioSettings;
use Drupal\Core\Database\Connection;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Changes portfolio body format from 'markdown' to 'text'.
 */
final class Deploy0001 implements ContainerInjectionInterface {

  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('database'),
    );
  }

  public function __construct(
    private Connection $database,
  ) {}

  public function __invoke(): string {
    $updated = $this->database
      ->update('node__body')
      ->fields(['body_format' => PortfolioSettings::TEXT_FORMAT])
      ->condition('bundle', 'portfolio')
      ->condition('body_format', PortfolioSettings::TEXT_FORMAT, '<>')
      ->execute();

    return \sprintf('Updated format for %d portfolio body fields.', $updated);
  }

}
