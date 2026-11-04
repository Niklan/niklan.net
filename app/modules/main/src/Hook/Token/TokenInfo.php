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

  public function __invoke(): array {
    return [
      'tokens' => $this->defineTokens(),
    ];
  }

  private function defineTokens(): array {
    return [
      'node' => $this->defineNodeTokens(),
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

}
