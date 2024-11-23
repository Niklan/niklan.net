((document, window) => {

  class Dialog {

    open = false;

    init() {
      this.$root.addEventListener('close', () => this.close());
      window.addEventListener('dialog:close', () => this.close());
    }

    show() {
      this.open = true;
      this.$root.showModal();
      document.querySelector('body').setAttribute('data-scroll-locked', '1');
    }

    close() {
      this.open = false;
      this.$root.close();
      document.querySelector('body').removeAttribute('data-scroll-locked');
    }

  }

  AlpineOnce(() => { window.Alpine.data('Dialog', () => new Dialog()); });

})(document, window);
