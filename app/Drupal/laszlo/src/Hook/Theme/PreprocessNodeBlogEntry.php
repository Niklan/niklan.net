<?php

declare(strict_types=1);

namespace Drupal\laszlo\Hook\Theme;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\external_content\Plugin\Field\FieldType\ExternalContentFieldItem;
use Drupal\media\MediaInterface;
use Drupal\niklan\Entity\File\FileInterface;
use Drupal\niklan\Entity\Node\BlogEntry;
use Drupal\niklan\Helper\TocBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class PreprocessNodeBlogEntry  {

  private function addFullVariables(BlogEntry $node, array &$variables): void {
    if ($node->get('external_content')->isEmpty()) {
      return;
    }

    $content = $node->get('external_content')->first();
    \assert($content instanceof ExternalContentFieldItem);

    $toc_builder = new TocBuilder();
    $variables['toc_links'] = $toc_builder->getTree($content);
  }

  private function addEstimatedReadTime(BlogEntry $node, array &$variables): void {
    $variables['estimated_read_time'] = $node->getEstimatedReadTime();
  }

  public function __invoke(array &$variables): void {
    $node = $variables['node'];
    \assert($node instanceof BlogEntry);
    $this->addEstimatedReadTime($node, $variables);
    $this->assPosterUri($node, $variables);

    match ($variables['view_mode']) {
      default => NULL,
      'full' => $this->addFullVariables($node, $variables),
    };
  }

  private function assPosterUri(BlogEntry $node, array &$variables): void {
    $variables['poster_uri'] = NULL;

    if ($node->get('field_media_image')->isEmpty()) {
      return;
    }

    $media = $node
      ->get('field_media_image')
      ->first()
      ->get('entity')
      ->getValue();

    if (!$media instanceof MediaInterface) {
      return;
    }

    $source_field = $media->getSource()->getConfiguration()['source_field'];
    $file = $media->get($source_field)->first()?->get('entity')->getValue();

    if (!$file instanceof FileInterface) {
      return;
    }

    $variables['poster_uri'] = $file->getFileUri();
  }

}
