<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Parser;

use Drupal\niklan\ExternalContent\Domain\Article;
use Drupal\niklan\ExternalContent\Domain\ArticleTranslation;
use Drupal\niklan\ExternalContent\Exception\ArticleParseException;
use Drupal\niklan\ExternalContent\Exception\XmlLoadException;
use Drupal\niklan\ExternalContent\Exception\XmlValidationException;
use Drupal\niklan\ExternalContent\Validation\XmlValidator;

final readonly class ArticleXmlParser {

  public function __construct(
    private XmlValidator $xmlValidator,
  ) {}

  /**
   * @throws \Drupal\niklan\ExternalContent\Exception\ArticleParseException
   */
  public function parse(string $file_path): Article {
    try {
      $this->xmlValidator->validate($file_path);
    }
    catch (XmlLoadException | XmlValidationException $exception) {
      throw new ArticleParseException($file_path, $exception->getMessage());
    }

    $dom = new \DOMDocument();
    $dom->load($file_path);
    $xpath = new \DOMXPath($dom);

    $article_node = $this->getArticleNode($xpath);
    $tags = $this->parseTags($xpath);
    $article = new Article(
      id: $article_node->getAttribute('id'),
      created: $article_node->getAttribute('created'),
      updated: $article_node->getAttribute('updated'),
      tags: $tags,
      directory: \dirname($file_path),
    );

    foreach ($this->parseTranslations($xpath) as $translation) {
      $article->addTranslation($translation);
    }

    return $article;
  }

  private function getArticleNode(\DOMXPath $xpath): \DOMElement {
    $article = $xpath->query('/article');
    \assert($article instanceof \DOMNodeList);
    $article_node = $article->item(0);
    \assert($article_node instanceof \DOMElement);

    return $article_node;
  }

  private function parseTags(\DOMXPath $xpath): array {
    $tags_list = $xpath->query('/article/tags/tag');
    \assert($tags_list instanceof \DOMNodeList);

    $tags = [];
    foreach ($tags_list as $tag_node) {
      \assert($tag_node instanceof \DOMElement);
      $tags[] = $tag_node->nodeValue;
    }

    return $tags;
  }

  private function parseTranslations(\DOMXPath $xpath): array {
    $translation_list = $xpath->query('/article/translations/translation');
    \assert($translation_list instanceof \DOMNodeList);

    $translations = [];
    foreach ($translation_list as $translation_node) {
      \assert($translation_node instanceof \DOMElement);
      $translations[] = $this->createTranslationFromNode($xpath, $translation_node);
    }

    return $translations;
  }

  private function createTranslationFromNode(\DOMXPath $xpath, \DOMElement $translation_node): ArticleTranslation {
    $is_primary = $this->getAttributeAsBoolean($translation_node, 'primary');

    $title = $this->getTextContent($xpath, $translation_node, 'title');
    $description = $this->getTextContent($xpath, $translation_node, 'description');
    $poster_path = $this->getAttributeFromElement($xpath, $translation_node, 'poster', 'src');

    return new ArticleTranslation(
      sourcePath: $translation_node->getAttribute('src'),
      language: $translation_node->getAttribute('language'),
      title: $this->cleanText($title),
      description: $this->cleanText($description),
      posterPath: $poster_path,
      isPrimary: $is_primary,
    );
  }

  private function getAttributeAsBoolean(\DOMElement $node, string $attribute): bool {
    return $node->hasAttribute($attribute) && $node->getAttribute($attribute) === 'true';
  }

  private function getTextContent(\DOMXPath $xpath, \DOMElement $parent, string $element_name): string {
    $node_list = $xpath->query($element_name, $parent);
    \assert($node_list instanceof \DOMNodeList);
    $node = $node_list->item(0);
    \assert($node instanceof \DOMElement);

    return (string) $node->nodeValue;
  }

  private function getAttributeFromElement(\DOMXPath $xpath, \DOMElement $parent, string $element_name, string $attribute): string {
    $node_list = $xpath->query($element_name, $parent);
    \assert($node_list instanceof \DOMNodeList);
    $node = $node_list->item(0);
    \assert($node instanceof \DOMElement);

    return $node->getAttribute($attribute);
  }

  private function cleanText(string $text): string {
    return (string) \preg_replace('/\s+/', ' ', \trim($text));
  }

}
