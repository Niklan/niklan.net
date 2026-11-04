<?php

declare(strict_types=1);

namespace Drupal\app_platform\Hook\Token;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Pager\PagerManagerInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\Core\Utility\Token;

/**
 * Provides [current-page:paged-text:TEXT] token.
 *
 * The @page placeholder in TEXT is replaced with the page number. The token is
 * empty on the first page. TEXT is not translated here: it is an
 * editor-provided value, translate it where it is stored (e.g. Metatag
 * configuration).
 */
final readonly class PagedTextToken {

  public function __construct(
    private PagerManagerInterface $pagerManager,
    private Token $token,
    private TranslationInterface $translation,
  ) {}

  /**
   * @return array<string, mixed>
   */
  #[Hook('token_info')]
  public function info(): array {
    return [
      'tokens' => [
        'current-page' => [
          'paged-text' => [
            'name' => $this->translation->translate('Text for paged pages'),
            'description' => $this->translation->translate('Text shown only on pages after the first. Use @page placeholder to insert page number.'),
            'dynamic' => TRUE,
          ],
        ],
      ],
    ];
  }

  /**
   * @param array<string, string> $tokens
   * @param array<string, mixed> $data
   * @param array<string, mixed> $options
   *
   * @return array<string, string>
   */
  #[Hook('tokens')]
  public function tokens(string $type, array $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata): array {
    if ($type !== 'current-page') {
      return [];
    }

    $paged_text_tokens = $this->token->findWithPrefix($tokens, 'paged-text');
    if (!$paged_text_tokens) {
      return [];
    }

    $pager = $this->pagerManager->getPager();
    if ($pager === NULL) {
      return [];
    }

    $bubbleable_metadata->addCacheContexts(['url.query_args.pagers:0']);
    $current_page = $pager->getCurrentPage();
    if ($current_page < 1) {
      return [];
    }

    $replacements = [];
    foreach ($paged_text_tokens as $name => $original) {
      $replacements[$original] = \str_replace('@page', (string) ($current_page + 1), $name);
    }

    return $replacements;
  }

}
