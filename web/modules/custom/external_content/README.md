# External Content

This module provides functionality to fetch content from external sources (local), process them and then pass it to plugins to do whatever you want with it.

Flow:


```mermaid
graph TB
  subgraph Configure Plugins
    ConfigurationPluginManager --> |Looking for external contet cnfigurations| MODULE.external_content.yml
    -->|Holds basic configuration infromation, including ID and working dir.| Configuration
  end

  subgraph Markup Plugins
    MarkupPluginManager --> |Provides plugins to convert specific markup into HTML| MarkupPlugin
    --> ConvertResult[A result string with HTML content.]
  end

  subgraph Grouper TODO
    ExternalContentGrouper --> |Each source file represents a single translation| ExternalContentTranslation
    --> |Multiple source files with the same ID combined into a collection| ExternalContent
    --> ExternalContentCollection
  end

  subgraph Element Parser Plugins TODO
    ElementParserPluginManager --> |Pass a single HTML element to parser| ElementParserPlugin
    --> ElementParserResult[A custom ElementInterface instance.]
  end

  subgraph Render Plugins TODO
    RenderPluginManager --> |Pass a single ElementInterface instance.| RenderPlugin
    --> RenderResult[A render array for that element.]
  end

  subgraph Source file finder
    SourceFileFinder -->|Looking for suitable files and adds them into collection| SourceFile
    -->|Each file value object holds all necessary information about file| SourceFileCollection
    --> SourceFile
  end

  subgraph Source file parser
    SourceFileParser --> ChainMarkupConverter
    --> MarkupPluginManager
    ConvertResult --> ChainElementParser
    --> ElementParserPluginManager
    ElementParserResult --> SourceFileContent
    SourceFileParser --> SourceFileParams
    --> |Contains information from FrontMatter| ParsedSourceFile
    SourceFileContent --> |Contains converted into HTML content| ParsedSourceFile
    --> ParsedSourceFileCollection
    --> ExternalContentGrouper
  end

  subgraph ExternalContentFinder TODO
    Configuration --> ExternalContentFinder
    --> SourceFileFinder
    SourceFileCollection --> SourceFileParser
  end

  subgraph Loader Plugins TODO
    LoaderPluginManager --> |Pass a single ExternalContent with all translations| LoaderPlugin
    --> LoaderResult
  end
```
