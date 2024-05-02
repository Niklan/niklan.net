<?php declare(strict_types = 1);

namespace Drupal\niklan\Extension\ExternalContent;

use Drupal\Core\Site\Settings;
use Drupal\external_content\Contract\Builder\EnvironmentBuilderInterface;
use Drupal\external_content\Contract\Extension\ConfigurableExtensionInterface;
use Drupal\external_content\Contract\Extension\ExtensionInterface;
use Drupal\external_content\Contract\ExternalContent\ExternalContentManagerInterface;
use League\Config\ConfigurationBuilderInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * {@selfdoc}
 *
 * @ingroup external_content
 */
final readonly class BlogExtension implements ExtensionInterface, ConfigurableExtensionInterface {

  /**
   * {@selfdoc}
   */
  public function __construct(
    private ExternalContentManagerInterface $externalContentManager,
    private EventDispatcherInterface $eventDispatcher,
  ) {}

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function register(EnvironmentBuilderInterface $environment): void {
    $extension_manager = $this->externalContentManager->getExtensionManager();
    $bundler_manager = $this->externalContentManager->getBundlerManager();
    $identifier_manager = $this
      ->externalContentManager
      ->getIdentifiersManager();
    $converter_manager = $this->externalContentManager->getConverterManager();
    $loader_manager = $this->externalContentManager->getLoaderManager();
    $serializer_manager = $this->externalContentManager->getSerializerManager();
    $builder_manager = $this
      ->externalContentManager
      ->getRenderArrayBuilderManager();
    $parser_manager = $this->externalContentManager->getHtmlParserManager();

    $environment
      // @todo Consider to replace by callable subscribers right here. Look
      //   like there are no need for Drupal's event dispatcher.
      ->setEventDispatcher($this->eventDispatcher)
      ->addExtension($extension_manager->get('file_finder'))
      ->addExtension($extension_manager->get('basic_html'))
      ->addBundler($bundler_manager->get('same_id'))
      ->addIdentifier($identifier_manager->get('front_matter'))
      ->addConverter($converter_manager->get('niklan_markdown'))
      ->addLoader($loader_manager->get('blog'))
      ->addSerializer($serializer_manager->get('drupal_media'))
      ->addRenderArrayBuilder($builder_manager->get('drupal_media'))
      ->addSerializer($serializer_manager->get('alert'))
      ->addHtmlParser($parser_manager->get('alert'))
      ->addRenderArrayBuilder($builder_manager->get('alert'))
      ->addRenderArrayBuilder($builder_manager->get('code_block'))
      ->addRenderArrayBuilder($builder_manager->get('link'));
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function configureSchema(ConfigurationBuilderInterface $builder): void {
    $builder->merge([
      'file_finder' => [
        'extensions' => ['md'],
        'directories' => [
          // @todo Check if its needed, provide a documentation and update
          //   scaffold files.
          Settings::get('external_content_blog_dir', 'private://test'),
        ],
      ],
    ]);
  }

}
