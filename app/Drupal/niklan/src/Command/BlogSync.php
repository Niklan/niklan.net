<?php declare(strict_types = 1);

namespace Drupal\niklan\Command;

use Drupal\Component\Utility\Timer;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\ExternalContent\ExternalContentManagerInterface;
use Drupal\external_content\Data\IdentifiedSourceBundleCollection;
use Drupal\external_content\Data\IdentifiedSourceCollection;
use Drupal\external_content\Data\SourceCollection;
use Drupal\external_content\Source\File;
use Drupal\niklan\Console\Style\NiklanStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * {@selfdoc}
 *
 * @ingroup content_sync
 */
#[AsCommand(
  name: 'niklan:sync:blog',
  description: 'Synchronize blog content from a provided directory.',
)]
final class BlogSync extends Command {

  /**
   * {@selfdoc}
   */
  private NiklanStyle $io;

  /**
   * Constructs a new BlogSync instance.
   */
  public function __construct(
    private readonly ExternalContentManagerInterface $externalContentManager,
  ) {
    parent::__construct();
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output): int {
    $this->io = new NiklanStyle($input, $output);
    $environment = $this
      ->externalContentManager
      ->getEnvironmentManager()
      ->get('blog');

    $sources = $this->find($environment);
    $identified_sources = $this->identify($sources, $environment);
    $this->bundle($identified_sources, $environment);

    //
    //    for ($i = 1; $i <= 10; $i++) {
    //      $this->io->advancePseudoProgress($i, 10, 'Processing file…');
    //    }.
    return self::SUCCESS;
  }

  /**
   * {@selfdoc}
   */
  private function find(EnvironmentInterface $environment): SourceCollection {
    $this->io->title("Start searching for sources…");
    Timer::start('find');
    $sources = $this
      ->externalContentManager
      ->getFinderManager()->find($environment);
    Timer::stop('find');

    $info = \sprintf('<fg=gray>Search took <options=bold;fg=gray>%sms</></>', Timer::read('find'));
    $this->io->info($info);

    $info = \sprintf("Found <options=bold;fg=green>%s</> source files…", \count($sources->items()));
    $this->io->info($info);

    return $sources;
  }

  /**
   * {@selfdoc}
   */
  private function identify(SourceCollection $sources, EnvironmentInterface $environment): IdentifiedSourceCollection {
    $this->io->title("Start identifying sources…");
    Timer::start('identify');
    $identified_sources = $this
      ->externalContentManager
      ->getIdentifiersManager()
      ->identify($sources, $environment);
    Timer::stop('identify');

    $info = \sprintf('<fg=gray>Identification took <options=bold;fg=gray>%sms</></>', Timer::read('identify'));
    $this->io->info($info);

    $info = \sprintf(
      'Identified <options=bold;fg=green>%s</> sources out of <options=bold;fg=green>%s</>…',
      \count($identified_sources->sources()),
      \count($sources->items()),
    );
    $this->io->info($info);

    foreach ($identified_sources->sources() as $identified_source) {
      \assert($identified_source->source instanceof File);
      $info = \sprintf(
        '<options=bold,underscore;fg=yellow>%s</> identified as <options=bold;fg=green>%s</> with <options=bold;fg=green>%s</> type…',
        $identified_source->source->getPathname(),
        $identified_source->id,
        $identified_source->source->type(),
      );
      $this->io->info($info);
    }

    return $identified_sources;
  }

  /**
   * {@selfdoc}
   */
  private function bundle(IdentifiedSourceCollection $identified_sources, EnvironmentInterface $environment): IdentifiedSourceBundleCollection {
    $this->io->title("Start bundling sources…");
    Timer::start('bundle');
    $bundled_sources = $this
      ->externalContentManager
      ->getBundlerManager()
      ->bundle($identified_sources, $environment);
    Timer::stop('bundle');

    $info = \sprintf(
      '<fg=gray>Bundling took <options=bold;fg=gray>%sms</></>',
      Timer::read('bundle'),
    );
    $this->io->info($info);

    $info = \sprintf(
      '<options=bold;fg=green>%s</> identified sources bundled into <options=bold;fg=green>%s</> bundles…',
      \count($identified_sources->sources()),
      \count($bundled_sources->bundles()),
    );
    $this->io->info($info);

    foreach ($bundled_sources->bundles() as $bundled_source) {
      $info = \sprintf(
        'Bundle <options=bold;fg=green>%s</> contains:',
        $bundled_source->id,
      );
      $this->io->info($info);

      foreach ($bundled_source->sources() as $identified_source) {
        $info = \sprintf(
          '<options=bold;fg=blue>%s</> with attributes: %s',
          $identified_source->id,
          \http_build_query(
            data: $identified_source->attributes->all(),
            arg_separator: '; ',
          ),
        );
        $this->io->info($info, '<fg=blue;options=bold> ==> •</>');
      }
    }

    return $bundled_sources;
  }

}
