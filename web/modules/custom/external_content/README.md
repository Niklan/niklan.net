# External Content

This module provides functionality to fetch content from external sources (local), process them and then pass it to plugins to do whatever you want with it.

Flow:


```mermaid
graph TB
  subgraph Configure Plugins
    ConfigurationPluginManager -->|Holds basic configuration infromation, including ID and working dir.| Configuration
  end

  subgraph Markup Plugins
    MarkupPluginManager --> |Provides plugins to convert specific markup into HTML| MarkupInterface
  end

  subgraph Markup Converter
    ChainMarkupConverter --> MarkupPluginManager --> SourceFileContent
  end

  subgraph Find
    SourceFileFinder -->|Looking for suitable files and adds them into collection| SourceFile
    SourceFile -->|Each file value object holds all necessary information about file| SourceFileCollection
    SourceFileCollection --> SourceFile
  end

  subgraph Parse
    SourceFile --> SourceFileParser
    SourceFileParser --> ChainMarkupConverter
    SourceFileParser --> ParsedSourceFile
    SourceFileParams --> |Contains information from FrontMatter| ParsedSourceFile
    SourceFileContent --> |Contains converted into HTML content| ParsedSourceFile
    ParsedSourceFile --> ParsedSourceFileCollection
  end
```
