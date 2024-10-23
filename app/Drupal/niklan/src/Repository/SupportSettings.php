<?php

declare(strict_types=1);

namespace Drupal\niklan\Repository;

use Drupal\Core\KeyValueStore\KeyValueFactoryInterface;
use Drupal\Core\KeyValueStore\KeyValueStoreInterface;
use Drupal\niklan\Contract\Repository\SupportSettings as SupportSettingsInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class SupportSettings implements SupportSettingsInterface {

  protected const string STORE_ID = 'niklan.support_settings';

  public function __construct(
    #[Autowire(service: 'keyvalue')]
    private KeyValueFactoryInterface $keyValueFactory,
  ) {}

  #[\Override]
  public function setBody(string $body): self {
    $this->store()->set('body', $body);

    return $this;
  }

  #[\Override]
  public function getBody(): string {
    return $this->store()->get('body', '');
  }

  #[\Override]
  public function setDonateUrl(string $url): self {
    $this->store()->set('donate_url', $url);

    return $this;
  }

  #[\Override]
  public function getDonateUrl(): string {
    return $this->store()->get('donate_url', '');
  }

  private function store(): KeyValueStoreInterface {
    return $this->keyValueFactory->get(self::STORE_ID);
  }

}
