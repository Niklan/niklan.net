<?php declare(strict_types = 1);

namespace Drupal\niklan\Bundler;

use Drupal\external_content\Contract\Bundler\BundlerInterface;
use Drupal\external_content\Contract\Bundler\BundlerResultInterface;
use Drupal\external_content\Data\Attributes;
use Drupal\external_content\Data\BundlerResult;
use Drupal\external_content\Node\ExternalContentDocument;

/**
 * Provides a bundler based on Front Matter 'id' and 'language' params.
 */
final class FrontMatterIdLanguageBundler implements BundlerInterface {

  /**
   * {@inheritdoc}
   */
  public function bundle(ExternalContentDocument $document): BundlerResultInterface {
    $file = $document->getFile();
    $data = $file->getData();

    if (!$data->has('front_matter')) {
      return BundlerResult::unidentified();
    }

    $front_matter = $data->get('front_matter');

    if (!\array_key_exists('id', $front_matter)) {
      return BundlerResult::unidentified();
    }

    $attributes = new Attributes();

    if (\array_key_exists('language', $front_matter)) {
      $attributes->setAttribute('language', $front_matter['language']);
    }

    return BundlerResult::identified($front_matter['id'], $attributes);
  }

}
