<?php

declare(strict_types=1);

namespace Drupal\app_platform\Http\Ajax;

use Drupal\Core\Ajax\CommandInterface;

final class HistoryReplaceStateCommand implements CommandInterface {

  public function __construct(
    protected string $url,
    protected ?array $stateObj = NULL,
  ) {}

  #[\Override]
  public function render(): array {
    return [
      'command' => 'niklanHistoryReplaceState',
      'stateObj' => $this->stateObj,
      'url' => $this->url,
    ];
  }

}
