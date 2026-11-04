<?php

declare(strict_types=1);

namespace Drupal\app_main\Hook\Token;

use Drupal\app_blog\Generator\BannerGenerator;
use Drupal\app_blog\Node\ArticleBundle;
use Drupal\app_contract\Utils\MediaHelper;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Render\BubbleableMetadata;

#[Hook('tokens')]
final readonly class Tokens {

  public function __construct(
    private BannerGenerator $bannerGenerator,
    private FileUrlGeneratorInterface $fileUrlGenerator,
  ) {}

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

  private function replaceNodeArticleBannerImage(string $original, State $state): void {
    $node = $state->getData()['node'];

    if (!$node instanceof ArticleBundle) {
      return;
    }

    $poster_uri = MediaHelper::getFileFromMediaField($node, 'field_media_image')?->getFileUri();

    if (!$poster_uri) {
      return;
    }

    // @phpstan-ignore-next-line
    $comment_count = $node->get('comment_node_blog_entry')->first()?->get('comment_count')->getCastedValue() ?? 0;
    $banner_uri = $this->bannerGenerator->generate($poster_uri, (string) $node->label(), (int) $node->getCreatedTime(), $comment_count);
    $state->getCacheableMetadata()->addCacheableDependency($node);

    if (!$banner_uri) {
      return;
    }

    $banner_url = $this->fileUrlGenerator->generateAbsoluteString($banner_uri);
    $state->setReplacement($original, $banner_url);
  }

  private function replaceNodeTokens(State $state): void {
    $state->replaceCallback('article-banner-image', $this->replaceNodeArticleBannerImage(...));
  }

}
