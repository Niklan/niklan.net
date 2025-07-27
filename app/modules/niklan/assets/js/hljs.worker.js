// https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/es/languages/LANGUAGE.min.js
const supportedLanguages = {
  'php': '/languages/php.min.js',
  'sql': '/languages/sql.min.js',
  'bash': '/languages/bash.min.js',
  'shell': '/languages/shell.min.js',
  'twig': '/languages/twig.min.js',
  'css': '/languages/css.min.js',
  'xml': '/languages/xml.min.js',
  'json': '/languages/json.min.js',
  'javascript': '/languages/javascript.min.js',
  'yaml': '/languages/yaml.min.js',
};

onmessage = async (event) => {
  try {
    const hljsModule = await import(`${event.data.esModulesBasePath}/highlight.min.js`);
    const hljs = hljsModule.default;

    if (event.data.language && supportedLanguages[event.data.language]) {
      const langModule = await import(`${event.data.esModulesBasePath}/${supportedLanguages[event.data.language]}`);
      hljs.registerLanguage(event.data.language, langModule.default);
    }

    let result;

    if (event.data.language && hljs.getLanguage(event.data.language)) {
      result = hljs.highlight(event.data.code, {
        language: event.data.language,
      });
    } else {
      result = hljs.highlightAuto(event.data.code);
    }

    postMessage(result.value);

  } catch (error) {
    postMessage({
      error: `Processing failed: ${error.message}`
    });
  }

};
