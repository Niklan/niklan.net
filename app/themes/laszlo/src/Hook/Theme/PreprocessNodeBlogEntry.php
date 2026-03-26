<?php

declare(strict_types=1);

namespace Drupal\laszlo\Hook\Theme;

use Drupal\app_blog\Node\ArticleBundle;
use Webmozart\Assert\Assert;

final readonly class PreprocessNodeBlogEntry {

  private const array SOFTWARE_LABELS = [
    'drupal' => 'Drupal',
    'php' => 'PHP',
    'docker' => 'Docker',
    'drush' => 'Drush',
    'twig' => 'Twig',
    'nginx' => 'Nginx',
    'apache' => 'Apache',
    'mysql' => 'MySQL',
    'mariadb' => 'MariaDB',
    'composer' => 'Composer',
    'node' => 'Node.js',
    'js' => 'JavaScript',
    'css' => 'CSS',
    'symfony' => 'Symfony',
    'linux' => 'Linux',
    'git' => 'Git',
  ];

  public function __invoke(array &$variables): void {
    $article = $variables['node'];
    \assert($article instanceof ArticleBundle);

    $this->addCompatibility($article, $variables);
  }

  private function addCompatibility(ArticleBundle $article, array &$variables): void {
    if (!$article->hasField('field_compatibility') || $article->get('field_compatibility')->isEmpty()) {
      $variables['compatibility'] = [];

      return;
    }

    $compatibility = [];
    foreach ($article->get('field_compatibility') as $item) {
      $name = $item->get('name')->getValue();
      Assert::string($name);
      $compatibility[] = [
        'name' => $name,
        'label' => self::SOFTWARE_LABELS[$name] ?? \ucfirst($name),
        'constraint' => $item->get('constraint')->getValue(),
      ];
    }

    $variables['compatibility'] = $compatibility;
  }

}
