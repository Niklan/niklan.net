<?php

use Drupal\external_content\Plugin\ExternalContent\Environment\EnvironmentManager;
use Drupal\niklan\ExternalContent\Domain\BlogArticleMarkdownSource;
use Drupal\niklan\Plugin\ExternalContent\Environment\BlogArticle;

$source = file_get_contents('private://content/blog/2021/09/29/drupal-warmer-2/article.ru.md');

$manager = \Drupal::service(EnvironmentManager::class);
$environment = $manager->createInstance(BlogArticle::ID);
\assert($environment instanceof BlogArticle);
$content = $environment->parse(new BlogArticleMarkdownSource($source));

$json = $environment->normalize($content);
$ast = $environment->denormalize($json);
