# External Content

This module provides functionality to fetch content from external sources (
local), process them and then pass it to plugins to do whatever you want with
it.

## Glossary

- **Configuration Plugin**: An external content configuration. Provided by
  via `*.external_content.yml` files.
- **Source File**: A representation of source file with a content. Holds
  information about working directory and it's URI where it found.
- **Markup Plugin**: Before processing content it should be converted to
  HTML markup and these plugins are responsible for that. E.g. Markdown
  plugin should convert Markdown into HTML.
- **HTML parser Plugin**: These plugins parse HTML into custom
  `ElementInterface` PHP DTO objects which is more lightweight and can be
  customized for specific elements and project needs.
- **Source File Content**: A collection of parsed `ElementInterface`
  elements using HTML parser plugins. Think of it like DOM Document.
- **Source File Params**: A simple DTO object with values from Front Matter
  of a source file.
- **Parsed Source File**: Contains information parsed from Source File. It
  will contain info about Source File, Source File Content and Source File
  Params.
- **Grouper Plugin**: Grouper plugin is responsible for grouping multiple
  versions of the same content at the single External Content.
- **External Content**: The DTO object contains information about
  specific content and its External Content Translations.
- **Loader Plugin**: Loader plugin is responsible for saving External
  Content into Drupal.
- **Builder Plugin**: Builder plugin is responsible for building render array
  from `ExternalContent` structure.

## Plugin System

### Configuration Plugins

These plugins are represented as `*.external_content.yml` files inside any
module. This file contains configurations for external content pipelines.

Example:

```yaml
# mymodule.external_content.yml
test:
  label: Testing
  working_dir: 'public://external-content'
```

In example above we defined `test` external content configuration with label
«Testing» and `working_dir` — which directs where to look for content. For
more information check `\Drupal\external_content\Plugin\ExternalContent
\Configuration\Configuration`.

Configuration plugins is a starting point for all external content.

```mermaid
graph TB
    ConfigurationPluginManager --> |Looking for external content configurations| MODULE.external_content.yml
    -->|Holds basic configuration information, including ID and working dir.| Configuration
```

### Markup Plugins

Markup plugins are responsible for converting any source files into HTML.
These plugins have `@ExternalContentMarkup` annotation.

### HTML Parser Plugins

HTML parser plugins are responsible for parsing HTML elements to a specific
PHP value objects representing a specific piece of content. These plugins
have `@ExternalContentHtmlParser` annotation.

### Grouper Plugins

Grouper plugins are responsible for grouping multiple source contents which
represents a single content but on different languages. These plugins have
`@ExternalContentGrouper` annotation.

### Loader Plugins

Loader plugins are responsible for CRUD operations for a single piece of
content. These plugins have `@ExternalContentLoader` annotation.

### Builder Plugins

Builder plugins are responsible for providing render array for specific piece
of content which is ready to be rendered. These plugins have
`@ExternalContentBuilder` annotation.

## Workflow

This is a basic representation of workflow. They are not strictly tied so
loader and can be called not at the same process as finders.

```mermaid
graph TB
  subgraph Configure Plugins
    ConfigurationPluginManager --> |Looking for external content configurations| MODULE.external_content.yml
    -->|Holds basic configuration information, including ID and working dir.| Configuration
  end

  subgraph External Content Finder Group[ExternalContentFinder]
    Configuration --> ExternalContentFinder
    ExternalContentFinder --> SourceFileFinder

    subgraph Source file finder
      SourceFileFinder -->|Looking for suitable files and adds them into collection| SourceFile
      -->|Each file value object holds all necessary information about file| SourceFileCollection
      --> SourceFile
    end

    SourceFileCollection --> SourceFileParser

    subgraph Source file parser
      SourceFileParser --> ChainMarkupConverter
      --> MarkupPluginManager

      subgraph Markup Plugins
        MarkupPluginManager --> |Provides plugins to convert specific markup into HTML| MarkupPlugin
        --> ConvertResult[A result string with HTML content.]
      end

      ConvertResult --> ChainHtmlParser
      --> HtmlParserPluginManager

      subgraph HTML Parser Plugins
        HtmlParserPluginManager --> |Pass a single HTML element to parser| HtmlParserPlugin
        --> HtmlParserResult[A custom ElementInterface instance.]
      end

      HtmlParserResult --> SourceFileContent
      SourceFileParser --> SourceFileParams
      --> |Contains information from FrontMatter| ParsedSourceFile
      SourceFileContent --> |Contains collection of parsed content elements| ParsedSourceFile
      --> ParsedSourceFileCollection
    end

    subgraph Parsed Source File Grouper
      ParsedSourceFileCollection --> ParsedSourceFileGrouper
      --> GrouperPluginManager

      subgraph Grouper Plugins
        GrouperPluginManager --> |Provides plugins to group parsed content into external content objects. Several files can represent a single content but in multiple languages, this plugins handle it.| GrouperPlugin
        GrouperPlugin --> |Each source file represents a single translation| ExternalContentTranslation
        --> |Multiple source files with the same ID combined into a collection| ExternalContent
        --> ExternalContentCollection
      end
    end
  end

  subgraph Loader Plugins
    LoaderPluginManager --> |Pass a single ExternalContent with all translations| LoaderPlugin
    --> LoaderResult
  end

  subgraph Builder Plugins
    BuilderPluginManager --> RenderArray[render array]
  end
```
