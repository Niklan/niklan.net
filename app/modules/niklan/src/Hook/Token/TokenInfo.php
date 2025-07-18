<?php

declare(strict_types=1);

namespace Drupal\niklan\Hook\Token;

use Drupal\Core\StringTranslation\TranslatableMarkup;

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
    ];
  }

  public function __invoke(): array {
    return [
      'tokens' => $this->defineTokens(),
    ];
  }

}
