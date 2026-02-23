<?php

declare(strict_types=1);

namespace Drupal\laszlo\Hook\Theme;

use Drupal\Core\DependencyInjection\ClassResolverInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\laszlo\Hook\Theme\PropsAlter\BrandingPropsAlter;
use Drupal\laszlo\Hook\Theme\PropsAlter\PageFooterPropsAlter;
use Drupal\laszlo\Hook\Theme\PropsAlter\PageHeaderPropsAlter;
use Drupal\app_blog\ExternalContent\Command\Sync;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class PreprocessPage implements ContainerInjectionInterface {

  public function __construct(
    private ClassResolverInterface $classResolver,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(ClassResolverInterface::class),
    );
  }

  private function prepareHeader(array &$variables): void {
    $headerAlter = $this->classResolver->getInstanceFromDefinition(PageHeaderPropsAlter::class);
    $brandingAlter = $this->classResolver->getInstanceFromDefinition(BrandingPropsAlter::class);

    $variables['header'] = [
      '#type' => 'component',
      '#component' => 'laszlo:page-header',
      '#propsAlter' => [$headerAlter(...)],
      '#props' => [
        'navigation' => [],
      ],
      '#slots' => [
        'branding' => [
          '#type' => 'component',
          '#component' => 'laszlo:branding',
          '#propsAlter' => [$brandingAlter(...)],
          '#props' => [
            'name' => '',
            'url' => '',
          ],
        ],
        'search' => [
          '#type' => 'component',
          '#component' => 'laszlo:search-bar',
          '#props' => [
            'placeholder' => new TranslatableMarkup('Site search'),
          ],
        ],
      ],
      '#cache' => [
        'keys' => ['laszlo', 'page', 'header'],
      ],
    ];
  }

  private function prepareFooter(array &$variables): void {
    $footerAlter = $this->classResolver->getInstanceFromDefinition(PageFooterPropsAlter::class);

    $variables['footer'] = [
      '#type' => 'component',
      '#component' => 'laszlo:page-footer',
      '#propsAlter' => [$footerAlter(...)],
      '#props' => [
        'versions' => [],
      ],
      '#cache' => [
        'keys' => ['laszlo', 'page', 'footer'],
        'tags' => [Sync::CACHE_TAG],
      ],
    ];
  }

  public function __invoke(array &$variables): void {
    $this->prepareHeader($variables);
    $this->prepareFooter($variables);
  }

}
