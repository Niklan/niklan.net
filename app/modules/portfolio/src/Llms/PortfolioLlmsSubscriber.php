<?php

declare(strict_types=1);

namespace Drupal\app_portfolio\Llms;

use Drupal\app_contract\Utils\MediaHelper;
use Drupal\app_platform\Llms\LlmsRenderEvent;
use Drupal\app_portfolio\Node\PortfolioBundle;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\media\MediaInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final readonly class PortfolioLlmsSubscriber implements EventSubscriberInterface {

  public function __construct(
    private TranslationInterface $translation,
    private FileUrlGeneratorInterface $fileUrlGenerator,
  ) {}

  public function onLlmsRender(LlmsRenderEvent $event): void {
    if ($event->routeMatch->getRouteName() !== 'entity.node.canonical') {
      return;
    }

    $node = $event->routeMatch->getParameter('node');

    if (!$node instanceof PortfolioBundle) {
      return;
    }

    $event->addCacheableDependency($node);

    $title = $node->getTitle();
    if ($title !== NULL) {
      $event->setTitle($title);
    }

    $parts = [];
    $parts[] = $this->buildMeta($node);
    $parts[] = $this->buildDescription($node);
    $parts[] = $this->buildImages($node);

    $event->setMarkdown(\implode("\n\n", \array_filter($parts)));
  }

  #[\Override]
  public static function getSubscribedEvents(): array {
    return [LlmsRenderEvent::class => 'onLlmsRender'];
  }

  private function buildMeta(PortfolioBundle $node): string {
    $lines = [];

    $year = $node->getYearOfCompletion();
    if ($year !== NULL) {
      $year_label = (string) $this->translation->translate('Year');
      $lines[] = \sprintf('- **%s**: %s', $year_label, $year);
    }

    $project_url = $node->getProjectUrl();
    if ($project_url !== NULL) {
      $url_label = (string) $this->translation->translate('Project URL');
      $lines[] = \sprintf('- **%s**: [%s](%s)', $url_label, $project_url, $project_url);
    }

    $categories = $node->getCategories();
    if ($categories !== []) {
      $cat_label = (string) $this->translation->translate('Categories');
      $lines[] = \sprintf('- **%s**:', $cat_label);

      foreach ($categories as $term) {
        $lines[] = \sprintf('  - %s', $term->label());
      }
    }

    return \implode("\n", $lines);
  }

  private function buildDescription(PortfolioBundle $node): ?string {
    $body = $node->get('body')->getString();

    return $body !== '' ? $body : NULL;
  }

  private function buildImages(PortfolioBundle $node): ?string {
    $media_items = $node->get('field_media_images')->referencedEntities();

    if ($media_items === []) {
      return NULL;
    }

    $label = (string) $this->translation->translate('Screenshots');
    $lines = [\sprintf('## %s', $label)];

    foreach ($media_items as $media) {
      $line = $this->buildImageLine($media);

      if ($line === NULL) {
        continue;
      }

      $lines[] = $line;
    }

    return \implode("\n", $lines);
  }

  private function buildImageLine(mixed $media): ?string {
    if (!$media instanceof MediaInterface) {
      return NULL;
    }

    $uri = MediaHelper::getFileUri($media);

    if ($uri === NULL) {
      return NULL;
    }

    $url = $this->fileUrlGenerator->generateAbsoluteString($uri);

    return \sprintf('![%s](%s)', $media->getName(), $url);
  }

}
