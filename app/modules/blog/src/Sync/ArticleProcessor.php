<?php

declare(strict_types=1);

namespace Drupal\app_blog\Sync;

use Drupal\app_blog\Sync\Domain\ArticleTranslation;
use Drupal\app_blog\Sync\Domain\ArticleProcessingContext;
use Drupal\app_blog\Sync\Domain\ProcessedArticle;
use Drupal\app_blog\Sync\Html\HtmlProcessor;
use Drupal\app_blog\Sync\Utils\EstimatedReadTimeCalculator;
use Drupal\app_contract\Contract\Media\MediaSynchronizer;
use Drupal\app_contract\Utils\PathHelper;
use Drupal\media\MediaInterface;
use League\CommonMark\MarkdownConverter;

final readonly class ArticleProcessor {

  public function __construct(
    private MarkdownConverter $markdownConverter,
    private HtmlProcessor $htmlProcessor,
    private MediaSynchronizer $mediaSynchronizer,
    private EstimatedReadTimeCalculator $readTimeCalculator,
  ) {}

  public function process(ArticleTranslation $translation, string $content_root): ProcessedArticle {
    $context = new ArticleProcessingContext($translation, $content_root);
    $html = $this->convertMarkdownToHtml($translation);
    $html = $this->htmlProcessor->process($html, $context);

    $source_path = $translation->contentDirectory . \DIRECTORY_SEPARATOR . $translation->sourcePath;

    return new ProcessedArticle(
      html: $html,
      sourcePathHash: PathHelper::hashRelativePath(path: $source_path, base_path: $content_root),
      estimatedReadTime: $this->readTimeCalculator->calculate($html),
      title: $translation->title,
      description: $translation->description,
      posterMedia: $this->syncPoster($translation),
      attachmentsMedia: $this->syncAttachments($translation),
    );
  }

  private function convertMarkdownToHtml(ArticleTranslation $translation): string {
    $markdown_path = $translation->contentDirectory . \DIRECTORY_SEPARATOR . $translation->sourcePath;
    $markdown = \file_get_contents($markdown_path);
    \assert($markdown !== FALSE);

    return $this->markdownConverter->convert($markdown)->getContent();
  }

  private function syncPoster(ArticleTranslation $translation): ?MediaInterface {
    $poster_path = $translation->contentDirectory . '/' . $translation->posterPath;
    return $this->mediaSynchronizer->sync($poster_path);
  }

  /**
   * @return list<\Drupal\media\MediaInterface>
   */
  private function syncAttachments(ArticleTranslation $translation): array {
    $attachments = [];
    foreach ($translation->getAttachments() as $attachment) {
      $path = $translation->contentDirectory . '/' . $attachment['src'];
      $media = $this->mediaSynchronizer->sync($path, ['title' => $attachment['title']]);
      if (!($media instanceof MediaInterface)) {
        continue;
      }

      $attachments[] = $media;
    }
    return $attachments;
  }

}
