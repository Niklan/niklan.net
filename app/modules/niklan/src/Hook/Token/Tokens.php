<?php

declare(strict_types=1);

namespace Drupal\niklan\Hook\Token;

use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Pager\PagerManagerInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Url;
use Drupal\niklan\Blog\Generator\BannerGenerator;
use Drupal\niklan\Node\Entity\BlogEntry;
use Drupal\niklan\Utils\MediaHelper;

#[Hook('tokens')]
final readonly class Tokens {

  public function __construct(
    private BannerGenerator $bannerGenerator,
    private FileUrlGeneratorInterface $fileUrlGenerator,
    private PagerManagerInterface $pagerManager,
  ) {}

  private function replaceNodeArticleBannerImage(string $original, State $state): void {
    $node = $state->getData()['node'];

    if (!$node instanceof BlogEntry) {
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

  private function replaceCurrentPageTokens(State $state): void {
    $state->replaceCallback('canonical-url', $this->replaceCurrentPageCanonicalUrl(...));
  }

  /**
   * @ingroup seo_pager
   */
  private function replaceCurrentPageCanonicalUrl(string $original, State $state): void {
    $state->getCacheableMetadata()->addCacheContexts(['route', 'url.query_args:page']);
    $options = ['absolute' => TRUE];

    if ($this->pagerManager->getPager()?->getTotalPages() > 0) {
      $options['query'] = ['page' => $this->pagerManager->getPager()->getCurrentPage()];
    }

    $url = Url::fromRoute('<current>');
    $state->setReplacement($original, $url->setOptions($options)->toString());
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
      'current-page' => $this->replaceCurrentPageTokens($state),
    };

    return $state->getReplacements();
  }

}
