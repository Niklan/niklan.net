<?php

declare(strict_types=1);

namespace Drupal\app_blog\Llms;

use Drupal\app_blog\Node\ArticleBundle;
use Drupal\app_platform\Llms\HtmlToMarkdownConverter;
use Drupal\app_platform\Llms\LlmsRenderEvent;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final readonly class ArticleLlmsSubscriber implements EventSubscriberInterface {

  public function __construct(
    private TranslationInterface $translation,
    private RendererInterface $renderer,
    private HtmlToMarkdownConverter $markdownConverter,
  ) {}

  public function onLlmsRender(LlmsRenderEvent $event): void {
    if ($event->routeMatch->getRouteName() !== 'entity.node.canonical') {
      return;
    }

    $node = $event->routeMatch->getParameter('node');

    if (!$node instanceof ArticleBundle) {
      return;
    }

    $event->addCacheableDependency($node);

    $title = $node->getTitle();
    if ($title !== NULL) {
      $event->setTitle($title);
    }

    $parts = [];
    $parts[] = $this->buildMeta($node, $event);
    $parts[] = $this->buildContent($node);

    $event->setMarkdown(\implode("\n\n", \array_filter($parts)));
  }

  #[\Override]
  public static function getSubscribedEvents(): array {
    return [LlmsRenderEvent::class => 'onLlmsRender'];
  }

  private function buildMeta(ArticleBundle $node, LlmsRenderEvent $event): string {
    $lines = [];

    $date_label = (string) $this->translation->translate('Date');
    $created = \date('Y-m-d', (int) $node->getCreatedTime());
    $lines[] = \sprintf('- **%s**: %s', $date_label, $created);

    $readTime = $node->getEstimatedReadTime();
    if ($readTime > 0) {
      $read_time_label = (string) $this->translation->translate('Estimated read time');
      $min_label = (string) $this->translation->translate('@count min', ['@count' => $readTime]);
      $lines[] = \sprintf('- **%s**: %s', $read_time_label, $min_label);
    }

    $tags = $node->get('field_tags')->referencedEntities();
    if ($tags !== []) {
      $tags_label = (string) $this->translation->translate('Tags');
      $lines[] = \sprintf('- **%s**:', $tags_label);

      foreach ($tags as $term) {
        $event->addCacheableDependency($term);
        $url = $term->toUrl()->toString();
        $lines[] = \sprintf('  - [%s](%s)', $term->label(), $url);
      }
    }

    return \implode("\n", $lines);
  }

  private function buildContent(ArticleBundle $node): ?string {
    $content = $node->getContent();

    if ($content === NULL) {
      return NULL;
    }

    $build = [
      '#type' => 'processed_text',
      '#text' => $content,
      '#format' => 'blog_article',
    ];

    $html = (string) $this->renderer->renderInIsolation($build);

    if ($html === '') {
      return NULL;
    }

    return $this->markdownConverter->convert($html);
  }

}
