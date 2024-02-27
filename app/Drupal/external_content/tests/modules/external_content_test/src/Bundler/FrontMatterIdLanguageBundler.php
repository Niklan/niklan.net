<?php declare(strict_types = 1);

namespace Drupal\external_content_test\Bundler;

use Drupal\external_content\Contract\Identifier\IdentifierInterface;
use Drupal\external_content\Contract\Source\SourceInterface;
use Drupal\external_content\Data\Attributes;
use Drupal\external_content\Data\IdentifiedSource;

/**
 * Provides a bundler based on Front Matter 'id' and 'language' params.
 */
final class FrontMatterIdLanguageBundler implements IdentifierInterface {

  /**
   * {@inheritdoc}
   */
  public function identify(SourceInterface $source): IdentifiedSource {
    $front_matter = $source->data()->get('front_matter');
    $attributes = new Attributes();

    if (\array_key_exists('language', $front_matter)) {
      $attributes->setAttribute('language', $front_matter['language']);
    }

    return new IdentifiedSource(
      source: $source,
      id: $front_matter['id'],
      attributes: $attributes,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function supportsIdentification(SourceInterface $source): bool {
    if (!$source->data()->has('front_matter')) {
      return FALSE;
    }

    return $source->data()->get('front_matter')->has('id');
  }

}
