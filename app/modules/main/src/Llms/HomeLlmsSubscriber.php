<?php

declare(strict_types=1);

namespace Drupal\app_main\Llms;

use Drupal\app_blog\Node\ArticleBundle;
use Drupal\app_platform\Llms\LlmsRenderEvent;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\Core\Url;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final readonly class HomeLlmsSubscriber implements EventSubscriberInterface {

  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
    private TranslationInterface $translation,
  ) {}

  public function onLlmsRender(LlmsRenderEvent $event): void {
    if ($event->routeMatch->getRouteName() !== 'app_main.home') {
      return;
    }

    $sections = $event->mainContent['#sections'] ?? [];
    $parts = [];

    if (isset($sections['home_intro'])) {
      $parts[] = (string) $sections['home_intro']['#heading'];
    }

    $parts[] = $this->buildLinks();

    foreach (['latest_posts', 'too_big_to_read', 'most_discussed'] as $section_id) {
      $section_markdown = $this->buildSection($sections, $section_id);

      if ($section_markdown === NULL) {
        continue;
      }

      $parts[] = $section_markdown;
    }

    $event->setMarkdown(\implode("\n\n", $parts));
  }

  #[\Override]
  public static function getSubscribedEvents(): array {
    return [LlmsRenderEvent::class => 'onLlmsRender'];
  }

  /**
   * @param array<string, mixed> $sections
   */
  private function buildSection(array $sections, string $section_id): ?string {
    if (!isset($sections[$section_id])) {
      return NULL;
    }

    $section = $sections[$section_id];
    \assert(\is_array($section));
    $items = $section['#items'] ?? [];
    \assert(\is_array($items));
    $ids = \array_keys($items);

    if ($ids === []) {
      return NULL;
    }

    $nodes = $this->entityTypeManager->getStorage('node')->loadMultiple($ids);
    $lines = $this->formatArticles($nodes);

    if ($lines === []) {
      return NULL;
    }

    $heading = $section['#heading'] ?? '';
    \assert(\is_string($heading) || $heading instanceof \Stringable);
    $heading = (string) $heading;

    return \sprintf("## %s\n\n%s", $heading, \implode("\n", $lines));
  }

  private function buildLinks(): string {
    $about = Url::fromRoute('app_main.about')->toString();
    $blog = Url::fromRoute('app_blog.blog_list')->toString();

    return \sprintf(
      "- [%s](%s)\n- [%s](%s)",
      $this->translation->translate('All publications'),
      $blog,
      $this->translation->translate('About the author'),
      $about,
    );
  }

  /**
   * @param array<int|string, \Drupal\Core\Entity\EntityInterface> $nodes
   *
   * @return list<string>
   */
  private function formatArticles(array $nodes): array {
    $lines = [];

    foreach ($nodes as $node) {
      if (!$node instanceof ArticleBundle) {
        continue;
      }

      $url = $node->toUrl()->toString();
      $title = $node->getTitle() ?? '';
      $date = \date('Y-m-d', (int) $node->getCreatedTime());
      $lines[] = \sprintf('- [%s](%s) (%s)', $title, $url, $date);
    }

    return $lines;
  }

}
