(() => {

  AlpineOnce(() => {
    window.Alpine.data('AiDialog', () => ({
      loading: false,
      copied: false,
      copyLabel: Drupal.t('Copy Markdown'),
      promptCopied: false,
      promptLabel: Drupal.t('Copy prompt'),

      resetCopyState() {
        this.copied = false;
        this.copyLabel = Drupal.t('Copy Markdown');
      },

      async copyPrompt() {
        const prompt = this.$root.dataset.aiPrompt;
        if (!prompt) {
          return;
        }

        try {
          await navigator.clipboard.writeText(prompt);
          this.promptCopied = true;
          this.promptLabel = Drupal.t('Copied!');
          setTimeout(() => {
            this.promptCopied = false;
            this.promptLabel = Drupal.t('Copy prompt');
          }, 3000);
        }
        catch (error) {
          // Silently handle errors.
        }
      },

      async copyMarkdown() {
        if (this.loading) {
          return;
        }

        const url = new URL(window.location.href);
        url.searchParams.set('_format', 'llms');
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
