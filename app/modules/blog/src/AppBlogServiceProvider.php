<?php

declare(strict_types=1);

namespace Drupal\app_blog;

use Drupal\app_blog\Controller\BlogList;
use Drupal\app_blog\Llms\ArticleLlmsSubscriber;
use Drupal\app_blog\Llms\BlogListLlmsSubscriber;
use Drupal\app_blog\Controller\RssFeed;
use Drupal\app_blog\Controller\RssFeedRedirect;
use Drupal\app_blog\Controller\RssFeedStylesheet;
use Drupal\app_blog\Sync\Parser\ArticleXmlParser;
use Drupal\app_blog\Sync\Validation\XmlValidator;
use Drupal\app_blog\Generator\BannerGenerator;
use Drupal\app_blog\Markup\Markdown\Extension\ArticleMarkdownExtension;
use Drupal\app_blog\Repository\DatabaseArticleRepository;
use Drupal\app_blog\SiteMap\BlogSiteMap;
use Drupal\app_blog\Sync\ArticleMapper;
use Drupal\app_blog\Sync\ArticleProcessor;
use Drupal\app_blog\Sync\ArticleSynchronizer;
use Drupal\app_blog\Sync\Contract\HtmlContentProcessor;
use Drupal\app_blog\Sync\Html\CalloutProcessor;
use Drupal\app_blog\Sync\Html\CodeBlockProcessor;
use Drupal\app_blog\Sync\Html\FigureProcessor;
use Drupal\app_blog\Sync\Html\HtmlProcessor;
use Drupal\app_blog\Sync\Html\LinkProcessor;
use Drupal\app_blog\Sync\Html\MediaProcessor;
use Drupal\app_blog\Sync\Utils\EstimatedReadTimeCalculator;
use Drupal\app_contract\Contract\Blog\ArticleRepository;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderInterface;
use League\CommonMark\Environment\Environment;
use League\CommonMark\MarkdownConverter;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Reference;

final readonly class AppBlogServiceProvider implements ServiceProviderInterface {

  #[\Override]
  public function register(ContainerBuilder $container): void {
    $autowire = static fn (string $class) => $container
      ->autowire($class)
      ->setPublic(TRUE)
      ->setAutoconfigured(TRUE);

    // Logger channels.
    $container->setDefinition(
      id: 'logger.channel.app_blog',
      definition: new ChildDefinition('logger.channel_base')->addArgument('app_blog'),
    );
    $container->setDefinition(
      id: 'logger.channel.app_blog.sync',
      definition: new ChildDefinition('logger.channel_base')->addArgument('app_blog.sync'),
    );

    // Repositories.
    $autowire(DatabaseArticleRepository::class);
    $container->setAlias(ArticleRepository::class, DatabaseArticleRepository::class);

    // Markdown environment & converter.
    $autowire(ArticleMarkdownExtension::class);

    $container->register('app_blog.markdown.environment', Environment::class)
      ->addMethodCall('addExtension', [new Reference(ArticleMarkdownExtension::class)]);
    $container->register('app_blog.markdown.converter', MarkdownConverter::class)
      ->addArgument(new Reference('app_blog.markdown.environment'));
    $container->setAlias(MarkdownConverter::class, 'app_blog.markdown.converter')->setPublic(TRUE);

    // Controllers.
    $autowire(BlogList::class);
    $autowire(RssFeed::class);
    $autowire(RssFeedRedirect::class);
    $autowire(RssFeedStylesheet::class);

    // Generator.
    $autowire(BannerGenerator::class);

    // SiteMap.
    $container->autowire(BlogSiteMap::class)
      ->setPublic(TRUE)
      ->setAutoconfigured(TRUE)
      ->addTag('app_sitemap');

    // XML parser & validation.
    $autowire(XmlValidator::class);
    $autowire(ArticleXmlParser::class);

    // Sync pipeline.
    $autowire(EstimatedReadTimeCalculator::class);
    $autowire(ArticleMapper::class);
    $autowire(ArticleProcessor::class);
    $autowire(ArticleSynchronizer::class);

    // HTML content processors — register interface for autoconfiguration.
    $container->registerForAutoconfiguration(HtmlContentProcessor::class)
      ->addTag(HtmlContentProcessor::class);

    $autowire(MediaProcessor::class);
    $autowire(FigureProcessor::class);
    $autowire(CalloutProcessor::class);
    $autowire(CodeBlockProcessor::class);
    $autowire(LinkProcessor::class);

    // HTML processor.
    $autowire(HtmlProcessor::class);

    // Llms.
    $autowire(ArticleLlmsSubscriber::class);
    $autowire(BlogListLlmsSubscriber::class);
  }

}
