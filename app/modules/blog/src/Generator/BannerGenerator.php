<?php

declare(strict_types=1);

namespace Drupal\app_blog\Generator;

use Drupal\app_image\DynamicImageStyle\DynamicImageStyle;
use Drupal\Component\Utility\Crypt;
use Drupal\Core\File\FileExists;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Template\TwigEnvironment;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mime\MimeTypeGuesserInterface;
use Symfony\Component\String\UnicodeString;

final readonly class BannerGenerator {

  private const int GENERATOR_VERSION = 1;
  private const string BANNER_DIRECTORY = 'public://blog-banners';

  public function __construct(
    private FileSystemInterface $fileSystem,
    private RendererInterface $renderer,
    #[Autowire(service: 'file.mime_type.guesser')]
    private MimeTypeGuesserInterface $mimeTypeGuesser,
    private TwigEnvironment $twig,
    private DynamicImageStyle $dynamicImageStyle,
  ) {}

  public function generate(string $poster_path, string $title, int $created, int $comment_count): ?string {
    if (!\file_exists($poster_path)) {
      return NULL;
    }

    $banner_uri = $this->prepareBannerUri($poster_path, $title, $created, $comment_count);

    if (\file_exists($banner_uri)) {
      return $banner_uri;
    }

    $directory = self::BANNER_DIRECTORY;
    if (!$this->fileSystem->prepareDirectory($directory, FileSystemInterface::CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS)) {
      return NULL;
    }

    try {
      $imagick = new \Imagick();
      $imagick->readImageBlob($this->renderSvg($poster_path, $title, $created, $comment_count));
      $imagick->setImageFormat('png32');
      $this->fileSystem->saveData($imagick->getImageBlob(), $banner_uri, FileExists::Replace);
    }
    catch (\Exception) {
      return NULL;
    }

    return $banner_uri;
  }

  private function prepareBannerUri(string $poster_path, string $title, int $created, int $comment_count): string {
    $hash = Crypt::hashBase64(self::GENERATOR_VERSION . $poster_path . \serialize([$title, $created, $comment_count]));

    return self::BANNER_DIRECTORY . "/$hash.png";
  }

  private function renderSvg(string $poster_path, string $title, int $created, int $comment_count): string {
    $builder = $this->dynamicImageStyle->effect('image_scale_and_crop', ['width' => 400, 'height' => 528]);
    $builder->createDerivative($poster_path);
    $poster_path = $builder->buildUri($poster_path);

    $mime = $this->mimeTypeGuesser->guessMimeType($poster_path);
    $base64 = "data:$mime;base64," . \base64_encode((string) \file_get_contents($poster_path));

    $build = [
      '#theme' => 'app_blog_article_banner',
      '#poster_base64' => $base64,
      '#text_lines' => \explode(\PHP_EOL, new UnicodeString($title)->wordwrap(20, \PHP_EOL, cut: TRUE)->toString()),
      '#created' => $created,
      '#comment_count' => $comment_count,
    ];

    // Make sure Twig debug comments are not rendered with SVG.
    $is_debug = $this->twig->isDebug();
    $this->twig->disableDebug();
    $svg = (string) $this->renderer->renderInIsolation($build);
    if ($is_debug) {
      $this->twig->enableDebug();
    }

    return $svg;
  }

}
