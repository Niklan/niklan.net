<?php

declare(strict_types=1);

namespace Drupal\app_blog;

use Drupal\app_blog\Controller\BlogList;
use Drupal\app_blog\ExternalContent\Extension\ArrayBuilderExtension;
use Drupal\app_blog\ExternalContent\Extension\ArrayParserExtension;
use Drupal\app_blog\ExternalContent\Extension\HtmlParserExtension;
use Drupal\app_blog\ExternalContent\Extension\RenderArrayBuilderExtension;
use Drupal\app_blog\ExternalContent\Nodes\ArticleLink\RenderArrayBuilder as ArticleLinkBuilder;
use Drupal\app_blog\ExternalContent\Nodes\Callout\RenderArrayBuilder as CalloutBuilder;
use Drupal\app_blog\ExternalContent\Nodes\CodeBlock\RenderArrayBuilder as CodeBlockBuilder;
use Drupal\app_blog\ExternalContent\Nodes\Footnote\RenderArrayBuilder as FootnoteBuilder;
use Drupal\app_blog\ExternalContent\Nodes\MediaReference\RenderArrayBuilder as MediaReferenceBuilder;
use Drupal\app_blog\ExternalContent\Parser\ArticleXmlParser;
use Drupal\app_blog\ExternalContent\Pipeline\ArticleProcessPipeline;
use Drupal\app_blog\ExternalContent\Pipeline\ArticleSyncPipeline;
use Drupal\app_blog\ExternalContent\Stages\ArticleFinder;
use Drupal\app_blog\ExternalContent\Stages\ArticleProcessor;
use Drupal\app_blog\ExternalContent\Stages\ArticleTranslationFieldUpdater;
use Drupal\app_blog\ExternalContent\Stages\AssetSynchronizer;
use Drupal\app_blog\ExternalContent\Stages\LinkProcessor;
use Drupal\app_blog\ExternalContent\Stages\MarkdownToAstParser;
use Drupal\app_blog\ExternalContent\Validation\XmlValidator;
use Drupal\app_blog\Generator\BannerGenerator;
use Drupal\app_blog\Markup\Markdown\Extension\ArticleMarkdownExtension;
use Drupal\app_blog\Repository\DatabaseArticleRepository;
use Drupal\app_blog\SiteMap\BlogSiteMap;
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
      definition: (new ChildDefinition('logger.channel_base'))->addArgument('app_blog'),
    );
    $container->setDefinition(
      id: 'logger.channel.app_blog.external_content',
      definition: (new ChildDefinition('logger.channel_base'))->addArgument('app_blog.external_content'),
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

    // Controller.
    $autowire(BlogList::class);

    // Generator.
    $autowire(BannerGenerator::class);

    // SiteMap.
    $container->autowire(BlogSiteMap::class)
      ->setPublic(TRUE)
      ->setAutoconfigured(TRUE)
      ->addTag('app_sitemap');

    // External Content: Validation & Parser.
    $autowire(XmlValidator::class);
    $autowire(ArticleXmlParser::class);

    // External Content: Stages.
    $autowire(MarkdownToAstParser::class);
    $autowire(AssetSynchronizer::class);
    $autowire(LinkProcessor::class);
    $autowire(ArticleTranslationFieldUpdater::class);
    $autowire(ArticleProcessor::class);
    $autowire(ArticleFinder::class);

    // External Content: Pipelines.
    $autowire(ArticleProcessPipeline::class);
    $autowire(ArticleSyncPipeline::class);

    // External Content: Node Render Array Builders.
    $autowire(ArticleLinkBuilder::class);
    $autowire(CalloutBuilder::class);
    $autowire(CodeBlockBuilder::class);
    $autowire(MediaReferenceBuilder::class);
    $autowire(FootnoteBuilder::class);

    // External Content: Extensions.
    $autowire(RenderArrayBuilderExtension::class);
    $autowire(HtmlParserExtension::class);
    $autowire(ArrayBuilderExtension::class);
    $autowire(ArrayParserExtension::class);
  }

}
