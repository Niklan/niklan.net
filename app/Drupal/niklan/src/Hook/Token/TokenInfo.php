<?php

declare(strict_types=1);

namespace Drupal\niklan\Hook\Token;

use Drupal\Core\StringTranslation\TranslatableMarkup;

final readonly class TokenInfo {

  private function defineTokens(): array {
    return [
      'node' => $this->defineNodeTokens(),
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

  public function __invoke(): array {
    return [
      'tokens' => $this->defineTokens(),
    ];
  }

}
