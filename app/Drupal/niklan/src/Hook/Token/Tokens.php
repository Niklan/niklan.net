<?php

declare(strict_types=1);

namespace Drupal\niklan\Hook\Token;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\niklan\Blog\Generator\BannerGenerator;
use Drupal\niklan\Node\Entity\BlogEntry;
use Drupal\niklan\Utils\MediaHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class Tokens implements ContainerInjectionInterface {

  public function __construct(
    private BannerGenerator $bannerGenerator,
    private FileUrlGeneratorInterface $fileUrlGenerator,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(BannerGenerator::class),
      $container->get(FileUrlGeneratorInterface::class),
    );
  }

  private function replaceNodeArticleBannerImage(string $original, State $state): void {
    $node = $state->getData()['node'];

    if (!$node instanceof BlogEntry) {
      return;
    }

    $poster_uri = MediaHelper::getFileFromMediaField($node, 'field_media_image')?->getFileUri();

    if (!$poster_uri) {
      return;
    }

    $banner_uri = $this->bannerGenerator->generate($poster_uri, $node->label());
    $banner_url = $this->fileUrlGenerator->generateAbsoluteString($banner_uri);
    $state->setReplacement($original, $banner_url);
    $state->getCacheableMetadata()->addCacheableDependency($node);
  }

  private function replaceNodeTokens(State $state): void {
    $state->replaceCallback('article-banner-image', $this->replaceNodeArticleBannerImage(...));
  }

  public function __invoke(string $type, array $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata): array {
    $state = new State(
      replacements: [],
      tokens: $tokens,
      data: $data,
      options: $options,
      bubbleableMetadata: $bubbleable_metadata,
    );

    match ($type) {
      default => NULL,
      'node' => $this->replaceNodeTokens($state),
    };

    return $state->getReplacements();
  }

}
