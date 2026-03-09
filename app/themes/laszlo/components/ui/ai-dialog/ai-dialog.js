(() => {

  AlpineOnce(() => {
    window.Alpine.data('AiDialog', () => ({
      loading: false,
      copied: false,
      copyLabel: Drupal.t('Copy Markdown'),

      resetCopyState() {
        this.copied = false;
        this.copyLabel = Drupal.t('Copy Markdown');
      },

      async copyMarkdown() {
        if (this.loading) {
          return;
        }

        const url = new URL(window.location.href);
        url.searchParams.set('_wrapper_format', 'llms');
        this.loading = true;

        try {
          const response = await fetch(url);
          const text = await response.text();
          await navigator.clipboard.writeText(text);
          this.copied = true;
          this.copyLabel = Drupal.t('Copied!');
          setTimeout(() => this.resetCopyState(), 5000);
        }
        catch (error) {
          // Silently handle errors.
        }
        finally {
          this.loading = false;
        }
      },
    }));
  });

})();
