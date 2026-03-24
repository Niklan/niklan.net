<?php

declare(strict_types=1);

namespace Drupal\app_blog\Plugin\Filter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\filter\Attribute\Filter;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\filter\Plugin\FilterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

#[Filter(
  id: self::ID,
  title: new TranslatableMarkup('Blog code block'),
  type: FilterInterface::TYPE_TRANSFORM_IRREVERSIBLE,
  description: new TranslatableMarkup('Replaces <app-code-block> placeholders with rendered SDC components.'),
  weight: 1,
)]
final class CodeBlockFilter extends FilterBase implements ContainerFactoryPluginInterface {

  public const string ID = 'app_blog_code_block';

  /**
   * @var array<string, mixed>
   */
  private array $attachments = [];

  #[\Override]
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get(RendererInterface::class),
    );
  }

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
    if (!\str_contains($text, '<app-code-block')) {
      return new FilterProcessResult($text);
    }

    $dom = Html::load($text);
    $elements = $this->collectByTag($dom, 'app-code-block');

    if (!$elements) {
      return new FilterProcessResult($text);
    }

    $this->attachments = [];

    foreach ($elements as $element) {
      $this->replaceCodeBlock($dom, $element);
    }

    $result = new FilterProcessResult(Html::serialize($dom));
    $result->setAttachments($this->attachments);

    return $result;
  }

  private function replaceCodeBlock(\DOMDocument $dom, \DOMElement $element): void {
    $build = [
      '#type' => 'component',
      '#component' => 'app_blog:code-block',
      '#props' => [
        'language' => $element->getAttribute('data-language') ?: NULL,
        'highlighted_lines' => $element->getAttribute('data-highlighted-lines') ?: NULL,
        'heading' => $element->getAttribute('data-header') ?: NULL,
        'code' => $element->textContent,
      ],
    ];
    $this->replaceDomNode($dom, $element, (string) $this->renderer->renderInIsolation($build));
    $this->attachments = BubbleableMetadata::mergeAttachments($this->attachments, $build['#attached'] ?? []);
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

}
