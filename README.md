# Niklan.net

<img src="./app/themes/laszlo/logo.svg" alt="Niklan.net" width="128" align="right">

[![Niklan.net website repository](https://img.shields.io/badge/website-blue?style=flat&logo=github&label=niklan.net)](https://github.com/Niklan/niklan.net)
[![Niklan.net content repository](https://img.shields.io/badge/content-f4f2ef?style=flat&logo=github&label=niklan.net)](https://github.com/Niklan/niklan.net-content)\
[![CI](https://github.com/Niklan/niklan.net/actions/workflows/ci.yml/badge.svg)](https://github.com/Niklan/niklan.net/actions/workflows/ci.yml)
[![CD](https://github.com/Niklan/niklan.net/actions/workflows/cd.yml/badge.svg)](https://github.com/Niklan/niklan.net/actions/workflows/cd.yml)

This is a repository containing the source code for the <https://niklan.net> website, built with Drupal 11.

> [!WARNING]
> It's not intended for use other than for educational purposes. Backward compatibility and support are not provided. If you want to ask me about that code, you can [contact me][contact-form] directly.

## 💿 System requirements

- PHP 8.4+
- [Task] for task automation with [Taskfile.yml]
- (optional) [Yarn] for running additional Quality Checks

## 📦 Content

The website content (articles, images, etc.) is stored in a [separate repository][content-repo] and managed independently of the codebase.

The `app_blog` module handles the full content pipeline: parsing Markdown files, synchronizing media assets, and creating blog posts as Drupal entities.

To sync content, run:

```bash
drush app:blog:sync
```

You can pass a relative path to sync a single article: `drush app:blog:sync blog/2024/my-article`. Use `--force` (`-f`) to re-sync content even if it is already up-to-date.

## 🖱️ Installation

- Clone the repository
  ```shell
  git clone https://github.com/Niklan/niklan.net.git && cd niklan.net
  ```
- Install the website
  ```shell
  task install
  ```
- Make a cup of ☕ or 🍵 and wait until the installation is finished.
- Open your local domain, e.g.: https://example.localhost

## 🔧 Custom settings

The project uses several custom `$settings` in `settings.php`:

| Setting | Description |
|---|---|
| `content_directory` | Absolute path to the content directory (e.g. `/mnt/content`) |
| `content_repository_url` | URL of the content repository, used for generating source links (e.g. `https://github.com/user/content`) |
| `website_repository_url` | URL of the website repository, used in the footer for version info (e.g. `https://github.com/user/website`) |
| `niklan_git_binary` | Path to the `git` binary (defaults to system `git`) |
| `telegram_token` | Telegram Bot API token for comment moderation |
| `telegram_secret_token` | Secret token for Telegram webhook verification |
| `telegram_chat_id` | Telegram chat ID for moderation notifications |
| `app_foresight` | (default: `TRUE`) Allows to disable the ForesightJS prefetch library for an environment. Only active for anonymous users. When disabled, no link prefetching is performed. |

## 🧬 Quality Tools

The project has multiple quality tools used for CI. You can run all of them with:

```bash
task validate
```

Many issues can be fixed automatically, for that, just run:

```bash
task fix
```

If you want to run a specific validation or fixing, use dedicated tasks, like `task validate/phpcs`. Check [Taskfile.yml] or run `task` to find all available options.

## 🧪 Testing

If you want to run all test suites at once, you can use:

```bash
task test
```

You can also run individual test suites like `task test/unit`, or bypass a custom command by:

```bash
task phpunit -- --filter FooBarTest
```

## 🪤 Contribution

This is a personal project, I don't expect any contributions to it. You can send a Pull Request with typo fixes and maybe other improvements, but don't expect them to be merged. You'd better not to do that.

## 📬 Feedback

If you want to ask me about this project or its code base, feel free to [contact me][contact-form].

[Task]: https://taskfile.dev/
[Taskfile.yml]: ./Taskfile.yml
[Yarn]: https://yarnpkg.com/
[content-repo]: https://github.com/Niklan/niklan.net-content
[contact-form]: https://niklan.net/contact
