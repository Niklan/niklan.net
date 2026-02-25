<?php

declare(strict_types=1);

namespace Drupal\app_blog\Controller;

use Drupal\app_blog\Node\ArticleBundle;
use Drupal\app_contract\Utils\MediaHelper;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Cache\CacheableResponse;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\Image\ImageFactory;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\Response;

final class RssFeed {

  private const int ITEMS_LIMIT = 20;

  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
    private LanguageManagerInterface $languageManager,
    private FileUrlGeneratorInterface $fileUrlGenerator,
    private ImageFactory $imageFactory,
    private ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * @return array<\Drupal\node\NodeInterface>
   */
  private function loadArticles(): array {
    $ids = $this->entityTypeManager
      ->getStorage('node')
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', 'blog_entry')
      ->condition('status', NodeInterface::PUBLISHED)
      ->condition('langcode', $this->languageManager->getCurrentLanguage()->getId())
      ->sort('created', 'DESC')
      ->range(0, self::ITEMS_LIMIT)
      ->execute();

    if ($ids === []) {
      return [];
    }

    return $this->entityTypeManager->getStorage('node')->loadMultiple($ids);
  }

  private function addItem(\DOMDocument $dom, \DOMElement $channel, ArticleBundle $node): void {
    $item = $dom->createElement('item');

    $title = $node->getTitle() ?? '';
    $item->appendChild($dom->createElement('title', $title));

    $node_url = (string) $node->toUrl()->setAbsolute()->toString();
    $item->appendChild($dom->createElement('link', $node_url));

    $guid = $dom->createElement('guid', $node->uuid() ?? '');
    $guid->setAttribute('isPermaLink', 'false');
    $item->appendChild($guid);

    $text = \strip_tags($node->get('body')->first()?->get('value')->getString() ?? '');
    if ($text !== '') {
      $description = $dom->createElement('description');
      $description->appendChild($dom->createCDATASection(\trim($text)));
      $item->appendChild($description);
    }

    $pub_date = \gmdate('r', (int) $node->getCreatedTime());
    $item->appendChild($dom->createElement('pubDate', $pub_date));

    $this->addMediaContent($dom, $item, $node);

    $channel->appendChild($item);
  }

  private function addMediaContent(\DOMDocument $dom, \DOMElement $item, ArticleBundle $node): void {
    $file = MediaHelper::getFileFromMediaField($node, 'field_media_image');
    if ($file === NULL) {
      return;
    }

    $file_uri = $file->getFileUri();
    \assert(\is_string($file_uri));
    $url = $this->fileUrlGenerator->generateAbsoluteString($file_uri);

    $media_content = $dom->createElement('media:content');
    $media_content->setAttribute('url', $url);
    $media_content->setAttribute('medium', 'image');

    $mime = $file->getMimeType();
    if (\is_string($mime)) {
      $media_content->setAttribute('type', $mime);
    }

    $image = $this->imageFactory->get($file_uri);
    if ($image->isValid()) {
      $media_content->setAttribute('width', (string) $image->getWidth());
      $media_content->setAttribute('height', (string) $image->getHeight());
    }

    $item->appendChild($media_content);
  }

  public function __invoke(): CacheableResponse {
    $dom = new \DOMDocument('1.0', 'UTF-8');
    $dom->formatOutput = TRUE;

    $xsl_url = Url::fromRoute('app_blog.rss_stylesheet')->setAbsolute()->toString();
    $dom->appendChild($dom->createProcessingInstruction(
      'xml-stylesheet',
      'type="text/xsl" href="' . $xsl_url . '"',
    ));

    $rss = $dom->createElement('rss');
    $rss->setAttribute('version', '2.0');
    $rss->setAttribute('xmlns:atom', 'http://www.w3.org/2005/Atom');
    $rss->setAttribute('xmlns:media', 'http://search.yahoo.com/mrss/');
    $dom->appendChild($rss);

    $channel = $dom->createElement('channel');
    $rss->appendChild($channel);

    $site_config = $this->configFactory->get('system.site');
    $site_name = $site_config->get('name');
    \assert(\is_string($site_name));
    $site_slogan = $site_config->get('slogan');
    \assert(\is_string($site_slogan));
    $base_url = (string) Url::fromRoute('<front>')->setAbsolute()->toString();
    $self_url = (string) Url::fromRoute('app_blog.rss_feed')->setAbsolute()->toString();
    $langcode = $this->languageManager->getCurrentLanguage()->getId();

    $channel->appendChild($dom->createElement('title', $site_name));
    $channel->appendChild($dom->createElement('description', $site_slogan));
    $channel->appendChild($dom->createElement('link', $base_url));
    $channel->appendChild($dom->createElement('language', $langcode));

    $atom_link = $dom->createElement('atom:link');
    $atom_link->setAttribute('href', $self_url);
    $atom_link->setAttribute('rel', 'self');
    $atom_link->setAttribute('type', 'application/rss+xml');
    $channel->appendChild($atom_link);

    $nodes = $this->loadArticles();

    if ($nodes !== []) {
      $last_changed = \max(\array_map(
        static fn (NodeInterface $node): int => (int) $node->getChangedTime(),
        $nodes,
      ));
      $channel->appendChild($dom->createElement('lastBuildDate', \gmdate('r', $last_changed)));
    }

    foreach ($nodes as $node) {
      \assert($node instanceof ArticleBundle);
      $this->addItem($dom, $channel, $node);
    }

    $xml = $dom->saveXML();
    \assert(\is_string($xml));

    // Using text/xml instead of application/rss+xml so that browsers apply
    // the XSL stylesheet. RSS readers parse XML regardless of Content-Type.
    $response = new CacheableResponse($xml, Response::HTTP_OK, [
      'Content-Type' => 'text/xml; charset=utf-8',
    ]);

    $cache = new CacheableMetadata();
    $cache->addCacheTags(['node_list:blog_entry']);
    $cache->addCacheContexts(['languages:language_interface']);
    $response->addCacheableDependency($cache);

    return $response;
  }

}
