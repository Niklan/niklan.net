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
    const { requestId, code, language, esModulesBasePath } = event.data;

    const hljsModule = await import(`${esModulesBasePath}/highlight.min.js`);
    const hljs = hljsModule.default;

    if (language && supportedLanguages[language]) {
      const langModule = await import(`${esModulesBasePath}/${supportedLanguages[language]}`);
      hljs.registerLanguage(language, langModule.default);
    }

    let result;

    if (language && hljs.getLanguage(language)) {
      result = hljs.highlight(code, { language });
    } else {
      result = hljs.highlightAuto(code);
    }

    postMessage({
      requestId,
      result: result.value
    });

  } catch (error) {
    postMessage({
      requestId: event.data.requestId,
      error: `Processing failed: ${error.message}`
    });
  }
};

