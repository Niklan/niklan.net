<?php

declare(strict_types=1);

namespace Drupal\app_blog\Controller;

use Drupal\app_contract\Contract\LanguageAwareStore\LanguageAwareFactory;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Cache\CacheableResponse;
use Drupal\Core\Extension\ExtensionPathResolver;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Theme\ThemeManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class RssFeedStylesheet {

  public function __construct(
    private ExtensionPathResolver $extensionPathResolver,
    private LanguageManagerInterface $languageManager,
    private ThemeManagerInterface $themeManager,
    private FileUrlGeneratorInterface $fileUrlGenerator,
    #[Autowire(service: 'keyvalue.language_aware')]
    private LanguageAwareFactory $languageAwareFactory,
  ) {}

  public function __invoke(): CacheableResponse {
    $module_path = $this->extensionPathResolver->getPath('module', 'app_blog');
    $xsl = \file_get_contents($module_path . '/assets/rss.xsl');
    \assert(\is_string($xsl));

    $theme = $this->themeManager->getActiveTheme();
    $logo_path = $theme->getPath() . '/logo.svg';
    $logo_url = $this->fileUrlGenerator->generateAbsoluteString($logo_path);

    $langcode = $this->languageManager->getCurrentLanguage()->getId();
    $store = $this->languageAwareFactory->get('niklan.home_settings', $langcode);
    $about_text = $store->get('description', '');
    \assert(\is_string($about_text));

    $xsl = \strtr($xsl, [
      '@langcode' => $langcode,
      '@logo_url' => $logo_url,
      '@about_text' => \strip_tags($about_text),
      '@banner_text' => (string) new TranslatableMarkup('This is an RSS feed. Copy the URL from the address bar into your RSS reader to subscribe.'),
      '@what_is_rss' => (string) new TranslatableMarkup('What is RSS?'),
      '@visit_site' => (string) new TranslatableMarkup('Visit website'),
      '@recent_posts' => (string) new TranslatableMarkup('Recent posts'),
    ]);

    $response = new CacheableResponse($xsl, CacheableResponse::HTTP_OK, [
      'Content-Type' => 'application/xml; charset=utf-8',
    ]);

    $cache = new CacheableMetadata();
    $cache->addCacheTags(['niklan.home_settings']);
    $cache->addCacheContexts(['languages:language_interface']);
    $response->addCacheableDependency($cache);

    return $response;
  }

}
