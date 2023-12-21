<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan\Kernel\Element;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\file\FileInterface;
use Drupal\media\MediaInterface;
use Drupal\media\MediaSourceInterface;
use Drupal\media\OEmbed\Provider;
use Drupal\media\OEmbed\Resource;
use Drupal\media\OEmbed\ResourceException;
use Drupal\media\OEmbed\ResourceFetcherInterface;
use Drupal\media\OEmbed\UrlResolverInterface;
use Drupal\responsive_image\Entity\ResponsiveImageStyle;
use Drupal\Tests\niklan\Kernel\NiklanTestBase;
use Drupal\user\Entity\User;
use Prophecy\Argument;

/**
 * Provides test for 'niklan_oembed_video' render element.
 *
 * @coversDefaultClass \Drupal\niklan\Element\OEmbedVideo
 */
final class OEmbedVideoTest extends NiklanTestBase {

  /**
   * Tests element without any information provided.
   */
  public function testEmptyElement(): void {
    $element = [
      '#type' => 'niklan_oembed_video',
    ];

    $this->render($element);

    self::assertCount(0, $this->cssSelect('.oembed-video'));
  }

  /**
   * Tests that validation doesn't allow non-media entities.
   */
  public function testMediaEntityValidation(): void {
    $this->installEntitySchema('user');
    $user = User::create(['name' => 'not_a_media']);
    $user->save();

    $element = [
      '#type' => 'niklan_oembed_video',
      '#media' => $user,
    ];

    $this->render($element);

    self::assertCount(0, $this->cssSelect('.oembed-video'));
  }

  /**
   * Tests that validation doesn't allow non 'oembed:video' plugins.
   */
  public function testOembedVideoPluginValidation(): void {
    $source = $this->prophesize(MediaSourceInterface::class);
    $source->getPluginId()->willReturn('foo:bar');

    $media = $this->prophesize(MediaInterface::class);
    $media->getSource()->willReturn($source->reveal());

    $element = [
      '#type' => 'niklan_oembed_video',
      '#media' => $media->reveal(),
    ];

    $this->render($element);

    self::assertCount(0, $this->cssSelect('.oembed-video'));
  }

  /**
   * Tests that validation checks for missing video URL.
   */
  public function testMissingVideoUrlValidation(): void {
    $source = $this->prophesize(MediaSourceInterface::class);
    $source->getPluginId()->willReturn('oembed:video');
    $source->getSourceFieldValue(Argument::any())->willReturn(NULL);

    $media = $this->prophesize(MediaInterface::class);
    $media->getSource()->willReturn($source->reveal());

    $element = [
      '#type' => 'niklan_oembed_video',
      '#media' => $media->reveal(),
    ];

    $this->render($element);

    self::assertCount(0, $this->cssSelect('.oembed-video'));
  }

  /**
   * Tests that validation checks resource availability.
   */
  public function testResourceAvailabilityValidation(): void {
    $element = [
      '#type' => 'niklan_oembed_video',
      '#media' => $this->buildOembedVideoMedia('https://youtu.be/throw'),
    ];

    $this->render($element);

    self::assertCount(0, $this->cssSelect('.oembed-video'));
  }

  /**
   * Builds a valid OEmbed video media entity prophecy.
   *
   * @param string $resource_url
   *   The resource URL,.
   *
   * @return \Drupal\media\MediaInterface
   *   The media entity.
   */
  protected function buildOembedVideoMedia(string $resource_url): MediaInterface {
    $source = $this->prophesize(MediaSourceInterface::class);
    $source->getPluginId()->willReturn('oembed:video');
    $source
      ->getSourceFieldValue(Argument::any())
      ->willReturn($resource_url);

    $media = $this->prophesize(MediaInterface::class);
    $media->getSource()->willReturn($source->reveal());

    $thumbnail_item_0_entity = $this->prophesize(FileInterface::class);
    $thumbnail_item_0_entity->getFileUri()->willReturn('public://image.jpg');

    $thumbnail_item_0 = new \stdClass();
    $thumbnail_item_0->entity = $thumbnail_item_0_entity->reveal();

    $thumbnail_items = $this->prophesize(FieldItemListInterface::class);
    $thumbnail_items->first()->willReturn($thumbnail_item_0);

    $media->get('thumbnail')->willReturn($thumbnail_items->reveal());
    $media->getCacheTags()->willReturn([]);

    return $media->reveal();
  }

  /**
   * Tests that validation checks for responsive image style.
   */
  public function testResponsiveImagePropertyValidation(): void {
    $element = [
      '#type' => 'niklan_oembed_video',
      '#media' => $this->buildOembedVideoMedia('https://youtu.be/oBpxD7Rcmjg'),
    ];

    $this->render($element);

    self::assertCount(0, $this->cssSelect('.oembed-video'));
  }

  /**
   * Tests that validation checks for responsive image style entity.
   */
  public function testResponsiveImageEntityValidation(): void {
    $element = [
      '#type' => 'niklan_oembed_video',
      '#media' => $this->buildOembedVideoMedia('https://youtu.be/oBpxD7Rcmjg'),
      '#preview_responsive_image_style' => 'foo',
    ];

    $this->render($element);

    self::assertCount(0, $this->cssSelect('.oembed-video'));
  }

  /**
   * Tests that validation checks for responsive image style mapping.
   */
  public function testResponsiveImageEntityWithoutMappingValidation(): void {
    $element = [
      '#type' => 'niklan_oembed_video',
      '#media' => $this->buildOembedVideoMedia('https://youtu.be/oBpxD7Rcmjg'),
      '#preview_responsive_image_style' => 'without_mapping',
    ];

    $this->render($element);

    self::assertCount(0, $this->cssSelect('.oembed-video'));
  }

  /**
   * Tests that validation checks for responsive image style mapping.
   */
  public function testYouTube(): void {
    $element = [
      '#type' => 'niklan_oembed_video',
      '#media' => $this->buildOembedVideoMedia('https://youtu.be/oBpxD7Rcmjg'),
      '#preview_responsive_image_style' => 'with_mapping',
    ];

    $this->render($element);

    self::assertCount(1, $this->cssSelect('.oembed-video'));
    self::assertCount(1, $this->cssSelect('.oembed-video__preview'));
    self::assertCount(1, $this->cssSelect('.oembed-video picture'));
    self::assertCount(1, $this->cssSelect('.oembed-video__content'));
    self::assertCount(1, $this->cssSelect('.oembed-video template'));
    self::assertCount(1, $this->cssSelect('.oembed-video iframe'));
  }

  /**
   * Tests that validation checks for responsive image style mapping.
   */
  public function testVimeo(): void {
    $element = [
      '#type' => 'niklan_oembed_video',
      '#media' => $this->buildOembedVideoMedia('https://vimeo.com/1'),
      '#preview_responsive_image_style' => 'with_mapping',
    ];

    $this->render($element);

    self::assertCount(1, $this->cssSelect('.oembed-video'));
    self::assertCount(1, $this->cssSelect('.oembed-video__preview'));
    self::assertCount(1, $this->cssSelect('.oembed-video picture'));
    self::assertCount(1, $this->cssSelect('.oembed-video__content'));
    self::assertCount(1, $this->cssSelect('.oembed-video template'));
    self::assertCount(1, $this->cssSelect('.oembed-video iframe'));
  }

  /**
   * Tests that validation checks for responsive image style mapping.
   */
  public function testIframeDomain(): void {
    $this
      ->config('media.settings')
      ->set('iframe_domain', 'https://niklan.localhost')
      ->save();

    $element = [
      '#type' => 'niklan_oembed_video',
      '#media' => $this->buildOembedVideoMedia('https://vimeo.com/1'),
      '#preview_responsive_image_style' => 'with_mapping',
    ];

    $this->render($element);

    self::assertRaw('https://niklan.localhost');
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $oembed_fetcher = $this->prophesize(ResourceFetcherInterface::class);
    $oembed_fetcher
      ->fetchResource('https://youtu.be/throw')
      ->willThrow(ResourceException::class);

    $provider = $this->prophesize(Provider::class);
    $provider->getName()->willReturn('YouTube');
    $resource = $this->prophesize(Resource::class);
    $resource->getProvider()->willReturn($provider->reveal());
    $resource->getWidth()->willReturn(1920);
    $resource->getHeight()->willReturn(1080);
    $resource->getTitle()->willReturn('YouTube Video');
    $oembed_fetcher
      ->fetchResource('https://youtu.be/oBpxD7Rcmjg')
      ->willReturn($resource->reveal());

    $provider = $this->prophesize(Provider::class);
    $provider->getName()->willReturn('Vimeo');
    $resource = $this->prophesize(Resource::class);
    $resource->getProvider()->willReturn($provider);
    $resource->getWidth()->willReturn(1920);
    $resource->getHeight()->willReturn(1080);
    $resource->getTitle()->willReturn('YouTube Video');
    $oembed_fetcher
      ->fetchResource('https://vimeo.com/1')
      ->willReturn($resource->reveal());

    $this
      ->container
      ->set('media.oembed.resource_fetcher', $oembed_fetcher->reveal());

    $url_resolver = $this->prophesize(UrlResolverInterface::class);
    $url_resolver->getResourceUrl(Argument::any())->willReturnArgument();
    $this->container->set('media.oembed.url_resolver', $url_resolver->reveal());

    ResponsiveImageStyle::create([
      'id' => 'without_mapping',
    ])->save();

    $style = ResponsiveImageStyle::create([
      'id' => 'with_mapping',
    ]);
    $style->addImageStyleMapping('test_breakpoint', '1x', [
      'image_mapping_type' => 'image_style',
      'image_mapping' => 'small',
    ]);
    $style->save();
  }

}
