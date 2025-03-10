<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Identifier;

use Drupal\external_content\Contract\Identifier\IdentifierInterface;
use Drupal\external_content\Contract\Source\SourceInterface;
use Drupal\external_content\Data\Attributes;
use Drupal\external_content\Data\IdentifiedSource;
use Drupal\external_content\Data\IdentifierResult;

/**
 * @ingroup external_content
 */
final class FrontMatter implements IdentifierInterface {

  #[\Override]
  public function identify(SourceInterface $source): IdentifierResult {
    if (!$this->supportsIdentification($source)) {
      return IdentifierResult::notIdentified();
    }

    /**
     * @var array{
     *   id: non-empty-string,
     *   language?: non-empty-string,
     * } $front_matter
     */
    $front_matter = $source->data()->get('front_matter');
    $attributes = new Attributes();

    if (\array_key_exists('language', $front_matter)) {
      $attributes->setAttribute('language', $front_matter['language']);
    }

    $result = new IdentifiedSource(
      id: $front_matter['id'],
      source: $source,
      attributes: $attributes,
    );

    return IdentifierResult::identified($result);
  }

  private function supportsIdentification(SourceInterface $source): bool {
    $data = $source->data();

    if (!$data->has('front_matter')) {
      return FALSE;
    }

    $front_matter = $data->get('front_matter');
    \assert(\is_array($front_matter));

    return \array_key_exists('id', $front_matter);
  }

}
