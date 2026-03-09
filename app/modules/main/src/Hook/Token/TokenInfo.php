<?php

declare(strict_types=1);

namespace Drupal\app_main\Hook\Token;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\TranslationInterface;

#[Hook('token_info')]
final readonly class TokenInfo {

  public function __construct(
    private TranslationInterface $stringTranslation,
  ) {}

  private function defineTokens(): array {
    return [
      'node' => $this->defineNodeTokens(),
      'current-page' => $this->defineCurrentPageTokens(),
    ];
  }

  private function defineNodeTokens(): array {
    return [
      'article-banner-image' => [
        'name' => $this->stringTranslation->translate('The article banner image'),
        'description' => $this->stringTranslation->translate('The banner used for social networks and messengers'),
      ],
    ];
  }

  private function defineCurrentPageTokens(): array {
    return [
      'canonical-url' => [
        'name' => $this->stringTranslation->translate('The canonical URL of the current page'),
        'description' => $this->stringTranslation->translate('The canonical URL of the current page.'),
      ],
      'pager-suffix' => [
        'name' => $this->stringTranslation->translate('Pager suffix'),
        'description' => $this->stringTranslation->translate('Returns a pager suffix (e.g. " — page #2") on paginated pages, empty on the first page.'),
      ],
    ];
  }

  public function __invoke(): array {
    return [
      'tokens' => $this->defineTokens(),
    ];
  }

}
