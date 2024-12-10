<?php

declare(strict_types=1);

namespace Drupal\niklan\Hook\Token;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Url;
use Drupal\niklan\Blog\Generator\BannerGenerator;
use Drupal\niklan\Node\Entity\BlogEntry;
use Drupal\niklan\Utils\MediaHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class Tokens implements ContainerInjectionInterface {

  public function __construct(
    private BannerGenerator $bannerGenerator,
    private FileUrlGeneratorInterface $fileUrlGenerator,
    private RequestStack $requestStack,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(BannerGenerator::class),
      $container->get(FileUrlGeneratorInterface::class),
      $container->get(RequestStack::class),
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

    // @phpstan-ignore-next-line
    $comment_count = $node->get('comment_node_blog_entry')->first()?->get('comment_count')->getCastedValue() ?? 0;
    $banner_uri = $this->bannerGenerator->generate(
      $poster_uri,
      $node->label(),
      (int) $node->getCreatedTime(),
      $comment_count,
    );
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
    $request = $this->requestStack->getCurrentRequest();
    $url = NULL;
    $options = ['absolute' => TRUE];

    if ($request->query->has('page')) {
      $options['query'] = ['page' => $request->query->get('page')];
    }

    try {
      $url = Url::createFromRequest($request);
    }
    catch (\Exception) {
      // Url::createFromRequest() can fail, e.g. on 404 pages.
      // Fall back and try again with Url::fromUserInput().
      try {
        $url = Url::fromUserInput($request->getPathInfo());
      }
      catch (\Exception) {
        // Instantiation would fail again on malformed urls.
      }
    }

    if (!$url instanceof Url) {
      return;
    }

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
