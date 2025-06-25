<?php

declare(strict_types=1);

namespace Drupal\external_content\Plugin\ExternalContent\Environment;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\external_content\Contract\Importer\ImporterSource;
use Drupal\external_content\Contract\Plugin\EnvironmentPlugin;
use Drupal\external_content\Nodes\Root;

#[Environment(
  id: self::ID,
  label: new TranslatableMarkup('Blog article'),
)]
final class Broken extends PluginBase implements EnvironmentPlugin {

  public const string ID = 'broken';

  public function parse(ImporterSource $source): Root {
    return new Root();
  }

  public function denormalize(string $json): Root {
    return new Root();
  }

  public function normalize(Root $content): string {
    return '';
  }

  public function view(Root $content, ViewRequest $request): array {
    return ['#markup' => 'Broken environment is used for this content.'];
  }

}
