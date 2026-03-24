<?php

declare(strict_types=1);

namespace Drupal\laszlo\Hook\Theme;

use Drupal\app_blog\Command\Sync;
use Drupal\Core\DependencyInjection\ClassResolverInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\laszlo\Hook\Theme\PropsAlter\BrandingPropsAlter;
use Drupal\laszlo\Hook\Theme\PropsAlter\PageFooterPropsAlter;
use Drupal\laszlo\Hook\Theme\PropsAlter\PageHeaderPropsAlter;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class PreprocessPage implements ContainerInjectionInterface {

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(ClassResolverInterface::class),
      $container->get(TranslationInterface::class),
    );
  }

  public function __construct(
    private ClassResolverInterface $classResolver,
    private TranslationInterface $stringTranslation,
  ) {}

  public function __invoke(array &$variables): void {
    $this->prepareHeader($variables);
    $this->prepareFooter($variables);
  }

  private function prepareHeader(array &$variables): void {
    $header_alter = $this->classResolver->getInstanceFromDefinition(PageHeaderPropsAlter::class);
    $branding_alter = $this->classResolver->getInstanceFromDefinition(BrandingPropsAlter::class);

    $variables['header'] = [
      '#type' => 'component',
      '#component' => 'laszlo:page-header',
      '#propsAlter' => [$header_alter(...)],
      '#props' => [
        'navigation' => [],
      ],
      '#slots' => [
        'branding' => [
          '#type' => 'component',
          '#component' => 'laszlo:branding',
          '#propsAlter' => [$branding_alter(...)],
          '#props' => [
            'name' => '',
            'url' => '',
          ],
        ],
        'search' => [
          '#type' => 'component',
          '#component' => 'laszlo:search-bar',
          '#props' => [
            'placeholder' => $this->stringTranslation->translate('Site search'),
          ],
        ],
      ],
      '#cache' => [
        'keys' => ['laszlo', 'page', 'header'],
      ],
    ];
  }

  private function prepareFooter(array &$variables): void {
    $footer_alter = $this->classResolver->getInstanceFromDefinition(PageFooterPropsAlter::class);

    $variables['footer'] = [
      '#type' => 'component',
      '#component' => 'laszlo:page-footer',
      '#propsAlter' => [$footer_alter(...)],
      '#props' => [
        'versions' => [],
      ],
      '#cache' => [
        'keys' => ['laszlo', 'page', 'footer'],
        'tags' => [Sync::CACHE_TAG],
      ],
    ];
  }

}
