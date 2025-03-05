<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Infrastructure;

use Drupal\niklan\ExternalContent\Domain\BlogArticle;
use Drupal\niklan\ExternalContent\Domain\BlogArticleTranslation;
use Drupal\niklan\ExternalContent\Exception\ArticleParseException;
use Drupal\niklan\ExternalContent\Exception\XmlLoadException;
use Drupal\niklan\ExternalContent\Exception\XmlValidationException;

final readonly class ArticleParser {

  public function __construct(
    private XmlValidator $xmlValidator,
  ) {}

  /**
   * @throws \Drupal\niklan\ExternalContent\Exception\ArticleParseException
   */
  public function parseFromXml(string $file_path): BlogArticle {
    try {
      $this->xmlValidator->validate($file_path);
    }
    catch (XmlLoadException | XmlValidationException $exception) {
      throw new ArticleParseException($file_path, $exception->getMessage());
    }

    return $this->parse($file_path);
  }

  private function parse(string $file_path): BlogArticle {
    $dom = new \DOMDocument();
    $dom->load($file_path);
    $xpath = new \DOMXPath($dom);

    $tags = [];
    foreach ($xpath->query('/article/tags/tag') as $tag_node) {
      \assert($tag_node instanceof \DOMElement);
      $tags[] = $tag_node->nodeValue;
    }

    $article_node = $xpath->query('/article')->item(0);
    \assert($article_node instanceof \DOMElement);
    $article = new BlogArticle(
      id: $article_node->getAttribute('id'),
      created: $article_node->getAttribute('created'),
      updated: $article_node->getAttribute('updated'),
      tags: $tags,
    );

    foreach ($xpath->query('/article/translations/translation') as $delta => $translation_node) {
      \assert($translation_node instanceof \DOMElement);
      $is_primary = FALSE;
      if ($translation_node->hasAttribute('primary')) {
        $is_primary = $translation_node->getAttribute('primary') === 'true';
      }

      $title_node = $xpath->query('title', $translation_node)->item(0);
      \assert($title_node instanceof \DOMElement);

      $description_node = $xpath->query('description', $translation_node)->item(0);
      \assert($description_node instanceof \DOMElement);

      $translation = new BlogArticleTranslation(
        sourcePath: $translation_node->getAttribute('src'),
        language: $translation_node->getAttribute('language'),
        title: \preg_replace('/\s+/', ' ', \trim($title_node->nodeValue)),
        description: \preg_replace('/\s+/', ' ', \trim($description_node->nodeValue)),
        posterPath: $xpath->query('poster', $translation_node)->item(0)->getAttribute('src'),
        isPrimary: $is_primary,
      );
      try {
        $article->addTranslation($translation);
      }
      catch (\InvalidArgumentException $exception) {
        throw new ArticleParseException($file_path, $exception->getMessage());
      }
    }

    return $article;
  }

}
