<?php

declare(strict_types=1);

namespace Drupal\app_main\Hook\Token;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\TranslatableMarkup;

#[Hook('token_info')]
final readonly class TokenInfo {

  private function defineTokens(): array {
    return [
      'node' => $this->defineNodeTokens(),
      'current-page' => $this->defineCurrentPageTokens(),
    ];
  }

  private function defineNodeTokens(): array {
    return [
      'article-banner-image' => [
        'name' => new TranslatableMarkup('The article banner image'),
        'description' => new TranslatableMarkup('The banner used for social networks and messengers'),
      ],
    ];
  }

  private function defineCurrentPageTokens(): array {
    return [
      'canonical-url' => [
        'name' => new TranslatableMarkup('The canonical URL of the current page'),
        'description' => new TranslatableMarkup('The canonical URL of the current page.'),
      ],
      'pager-suffix' => [
        'name' => new TranslatableMarkup('Pager suffix'),
        'description' => new TranslatableMarkup('Returns a pager suffix (e.g. " — page #2") on paginated pages, empty on the first page.'),
      ],
    ];
  }

  public function __invoke(): array {
    return [
      'tokens' => $this->defineTokens(),
    ];
  }

}
