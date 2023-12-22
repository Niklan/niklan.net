<?php declare(strict_types = 1);

namespace Drupal\niklan\Identifier;

use Drupal\external_content\Contract\Identifier\IdentifierInterface;
use Drupal\external_content\Contract\Source\SourceInterface;
use Drupal\external_content\Data\Attributes;
use Drupal\external_content\Data\SourceIdentification;

/**
 * Provides a bundler based on Front Matter 'id' and 'language' params.
 */
final class FrontMatterIdentifier implements IdentifierInterface {

  /**
   * {@inheritdoc}
   */
  public function identify(SourceInterface $source): SourceIdentification {
    $front_matter = $source->data()->get('front_matter');
    $attributes = new Attributes();

    if (\array_key_exists('language', $front_matter)) {
      $attributes->setAttribute('language', $front_matter['language']);
    }

    return new SourceIdentification($front_matter['id'], $attributes);
  }

  /**
   * {@inheritdoc}
   */
  public function supportsIdentification(SourceInterface $source): bool {
    $data = $source->data();

    if (!$data->has('front_matter')) {
      return FALSE;
    }

    $front_matter = $data->get('front_matter');

    if (!\array_key_exists('id', $front_matter)) {
      return FALSE;
    }

    return TRUE;
  }

}
