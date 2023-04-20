<?php declare(strict_types = 1);

namespace Drupal\external_content\Plugin\ExternalContent\Grouper;

use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\external_content\Data\ExternalContent;
use Drupal\external_content\Data\ExternalContentCollection;
use Drupal\external_content\Data\ParsedSourceFile;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a basic grouper by content params.
 *
 * This plugin groups content by their 'id' and 'language' params (Front
 * Matter).
 *
 * Example:
 *
 * @code
 * ---
 * id: foo-bar
 * language: ru
 * ---
 *
 * Content.
 * @endcode
 *
 * The 'language' param is optional and if not set will be replaced by current
 * site default language.
 *
 * @ExternalContentGrouper(
 *   id = "params",
 *   label = @Translation("Params"),
 * )
 */
final class Params extends GrouperPlugin implements ContainerFactoryPluginInterface {

  /**
   * The language manager.
   */
  protected LanguageManagerInterface $languageManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    $instance = new self($configuration, $plugin_id, $plugin_definition);
    $instance->languageManager = $container->get('language_manager');

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  protected static function isApplicable(ParsedSourceFile $parsed_file): bool {
    return $parsed_file->getParams()->has('id');
  }

  /**
   * {@inheritdoc}
   */
  protected function doGroup(ParsedSourceFile $parsed_file, ExternalContentCollection $collection): void {
    $params = $parsed_file->getParams();
    $id = $params->get('id');
    // If language is not provided, use sites default.
    $language = $params->get('language') ?? $this->getDefaultLanguageId();

    $external_content = $collection->get($id) ?? new ExternalContent($id);
    $external_content->addTranslation($language, $parsed_file);

    $collection->add($external_content);
  }

  /**
   * Gets sites default language ID.
   *
   * @return string
   *   The language ID.
   */
  public function getDefaultLanguageId(): string {
    return $this->languageManager->getDefaultLanguage()->getId();
  }

}
