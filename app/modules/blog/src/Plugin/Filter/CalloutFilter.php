<?php

declare(strict_types=1);

namespace Drupal\app_blog\Plugin\Filter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\filter\Attribute\Filter;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\filter\Plugin\FilterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

#[Filter(
  id: self::ID,
  title: new TranslatableMarkup('Blog callout'),
  type: FilterInterface::TYPE_TRANSFORM_IRREVERSIBLE,
  description: new TranslatableMarkup('Replaces <app-callout> placeholders with rendered SDC components.'),
  weight: 0,
)]
final class CalloutFilter extends FilterBase implements ContainerFactoryPluginInterface {

  public const string ID = 'app_blog_callout';

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly RendererInterface $renderer,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  #[\Override]
  public function process($text, $langcode): FilterProcessResult {
    if (!\str_contains($text, '<app-callout')) {
      return new FilterProcessResult($text);
    }

    $dom = Html::load($text);
    $elements = $this->collectByTag($dom, 'app-callout');

    if (!$elements) {
      return new FilterProcessResult($text);
    }

    foreach ($elements as $element) {
      $this->replaceCallout($dom, $element);
    }

    return new FilterProcessResult(Html::serialize($dom));
  }

  #[\Override]
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get(RendererInterface::class),
    );
  }

  private function replaceCallout(\DOMDocument $dom, \DOMElement $element): void {
    $build = [
      '#type' => 'component',
      '#component' => 'app_blog:callout',
      '#props' => ['type' => $element->getAttribute('data-type') ?: 'note'],
      '#slots' => $this->extractSlots($dom, $element),
    ];
    $this->replaceDomNode($dom, $element, (string) $this->renderer->renderInIsolation($build));
  }

  private function extractSlots(\DOMDocument $dom, \DOMElement $element): array {
    $slot_map = ['app-callout-title' => 'title', 'app-callout-body' => 'body'];
    $slots = [];
    foreach ($element->childNodes as $child) {
      if (!$child instanceof \DOMElement || !isset($slot_map[$child->tagName])) {
        continue;
      }
      $slots[$slot_map[$child->tagName]] = ['#markup' => $this->innerHtml($child, $dom)];
    }
    return $slots;
  }

  private function collectByTag(\DOMDocument $dom, string $tag): array {
    $elements = [];
    foreach ($dom->getElementsByTagName($tag) as $node) {
      $elements[] = $node;
    }
    return $elements;
  }

  private function replaceDomNode(\DOMDocument $dom, \DOMElement $element, string $html): void {
    $replacement = Html::load($html);
    $body = $replacement->getElementsByTagName('body')->item(0);

    if (!$body instanceof \DOMElement) {
      return;
    }

    // iterator_to_array() is required: importNode() copies without removing
    // from source, so while ($body->firstChild) would loop infinitely.
    $fragment = $dom->createDocumentFragment();
    foreach (\iterator_to_array($body->childNodes) as $child) {
      $fragment->appendChild($dom->importNode($child, TRUE));
    }

    $element->parentNode?->replaceChild($fragment, $element);
  }

  private function innerHtml(\DOMElement $element, \DOMDocument $dom): string {
    $html = '';
    foreach ($element->childNodes as $child) {
      $html .= $dom->saveHTML($child);
    }
    return $html;
  }

}
