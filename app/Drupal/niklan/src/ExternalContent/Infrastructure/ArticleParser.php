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

    $dom = new \DOMDocument();
    $dom->load($file_path);
    $xpath = new \DOMXPath($dom);

    $articleNode = $this->getArticleNode($xpath);
    $tags = $this->parseTags($xpath);
    $article = new BlogArticle(
      id: $articleNode->getAttribute('id'),
      created: $articleNode->getAttribute('created'),
      updated: $articleNode->getAttribute('updated'),
      tags: $tags,
    );

    foreach ($this->parseTranslations($xpath) as $translation) {
      $article->addTranslation($translation);
    }

    return $article;
  }

  private function getArticleNode(\DOMXPath $xpath): \DOMElement {
    $node = $xpath->query('/article')->item(0);
    \assert($node instanceof \DOMElement);

    return $node;
  }

  private function parseTags(\DOMXPath $xpath): array {
    $tags = [];
    foreach ($xpath->query('/article/tags/tag') as $tagNode) {
      \assert($tagNode instanceof \DOMElement);
      $tags[] = $tagNode->nodeValue;
    }

    return $tags;
  }

  private function parseTranslations(\DOMXPath $xpath): array {
    $translations = [];
    foreach ($xpath->query('/article/translations/translation') as $translationNode) {
      \assert($translationNode instanceof \DOMElement);
      $translations[] = $this->createTranslationFromNode($xpath, $translationNode);
    }

    return $translations;
  }

  private function createTranslationFromNode(\DOMXPath $xpath, \DOMElement $translationNode): BlogArticleTranslation {
    $isPrimary = $this->getAttributeAsBoolean($translationNode, 'primary');

    $title = $this->getTextContent($xpath, $translationNode, 'title');
    $description = $this->getTextContent($xpath, $translationNode, 'description');
    $posterPath = $this->getAttributeFromElement($xpath, $translationNode, 'poster', 'src');

    return new BlogArticleTranslation(
      sourcePath: $translationNode->getAttribute('src'),
      language: $translationNode->getAttribute('language'),
      title: $this->cleanText($title),
      description: $this->cleanText($description),
      posterPath: $posterPath,
      isPrimary: $isPrimary,
    );
  }

  private function getAttributeAsBoolean(\DOMElement $node, string $attribute): bool {
    return $node->hasAttribute($attribute) && $node->getAttribute($attribute) === 'true';
  }

  private function getTextContent(\DOMXPath $xpath, \DOMElement $parent, string $elementName): string {
    $node = $xpath->query($elementName, $parent)->item(0);
    \assert($node instanceof \DOMElement);

    return $node->nodeValue;
  }

  private function getAttributeFromElement(\DOMXPath $xpath, \DOMElement $parent, string $elementName, string $attribute): string {
    $node = $xpath->query($elementName, $parent)->item(0);
    \assert($node instanceof \DOMElement);

    return $node->getAttribute($attribute);
  }

  private function cleanText(string $text): string {
    return \preg_replace('/\s+/', ' ', \trim($text));
  }

}
