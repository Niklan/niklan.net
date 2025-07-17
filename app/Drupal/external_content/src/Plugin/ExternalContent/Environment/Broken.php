<?php

declare(strict_types=1);

namespace Drupal\external_content\Plugin\ExternalContent\Environment;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\external_content\Contract\Plugin\EnvironmentPlugin;
use Drupal\external_content\Nodes\Document;

/**
 * @implements \Drupal\external_content\Contract\Plugin\EnvironmentPlugin<string>
 */
#[Environment(
  id: self::ID,
  label: new TranslatableMarkup('Blog article'),
)]
final class Broken extends PluginBase implements EnvironmentPlugin {

  public const string ID = 'broken';

  public function parse(mixed $source): Document {
    return new Document();
  }

  public function denormalize(string $json): Document {
    return new Document();
  }

  public function normalize(Document $content): string {
    return '';
  }

  public function view(Document $content, ViewRequest $request): array {
    return ['#markup' => 'Broken environment is used for this content.'];
  }

}
