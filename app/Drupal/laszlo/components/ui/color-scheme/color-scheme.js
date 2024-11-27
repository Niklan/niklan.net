((Drupal, ColorScheme) => {
  
  const updateElement = (element, color, isSystem) => {
    const value = isSystem ? 'system' : color;
    const radioElement = element.querySelector(`[data-mode-option][value="${value}"]`);
    radioElement.checked = true;
  }

  Drupal.behaviors.ColorSchemeToggle = {
    attach(context) {
      once('color-scheme', '[data-selector="color-scheme"]', context).forEach(toggleElement => {
        // Initial value.
        updateElement(toggleElement, ColorScheme.getColorScheme(), ColorScheme.isSchemeFromSystem());

        ColorScheme.onUpdate((color, isSystem) => {
          updateElement(toggleElement, color, isSystem);
        });

        toggleElement.querySelectorAll('[data-mode-option]').forEach(optionElement => {
          optionElement.addEventListener('click', () => {
            ColorScheme.setColorScheme(optionElement.value);
          });
        });
      });
    }
  }

})(Drupal, ColorScheme);
