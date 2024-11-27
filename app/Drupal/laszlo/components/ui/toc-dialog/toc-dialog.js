((document, window) => {

  AlpineOnce(() => {
    window.Alpine.data('ToCDialog', () => ({
      query: '',

      isVisible(element) {
        const regex = new RegExp(`${this.query}`, 'i');

        if (element.textContent.search(regex) === -1) {
          return false;
        }

        return true;
      }
    }));
  });

})(document, window);
