# Niklan.net

<img src="./app/Drupal/laszlo/logo.svg" alt="Niklan.net" width="128" align="right">

[![Niklan.net website repository](https://img.shields.io/badge/website-blue?style=flat&logo=github&label=niklan.net)](https://github.com/Niklan/niklan.net)
[![Niklan.net content repository](https://img.shields.io/badge/content-f4f2ef?style=flat&logo=github&label=niklan.net)](https://github.com/Niklan/niklan.net-content)\
[![CI](https://github.com/Niklan/niklan.net/actions/workflows/ci.yml/badge.svg)](https://github.com/Niklan/niklan.net/actions/workflows/ci.yml)
[![CD](https://github.com/Niklan/niklan.net/actions/workflows/cd.yml/badge.svg)](https://github.com/Niklan/niklan.net/actions/workflows/cd.yml)

This is a repository containing the source code for the <https://niklan.net>
website.

> [!WARNING]
> It's not intended for use other than for educational purposes. Backward
> compatibility and support are not provided. If you want to ask me about that
> code, you can [contact me][contact-form] directly.

## 💿 System requirements

- [Drupal's system requirements][drupal-system-requirements] if not mentioned 
  below.
- PHP 8.3+
- (optional) [Task] for simplify things with [Taskfile.yml].
- (optional) Yarn for running additional Quality Checks.

## 🖱️ Installation

- Clone repository
  ```shell
  git clone https://github.com/Niklan/niklan.net.git
  ```
- Install website
  ```shell
  task install
  ```
- Make a cup of ☕ or 🍵 and wait until the installation is finished.
- Open your local domain, e.g.: https://example.localhost

## 🧬 Quality Tools

The project has multiple quality tools used for CI. You can run all of them 
with:

```bash
task validate
```

Many issues can be fixed automatically, for that, just run:

```bash
task fix
```

If you want to run a specific validation or fixing, use dedicated tasks, like
`task validate/phpcs`. Check [Taskfile.yml] or run `task` to find all available
options.

## 🧪 Testing

f you want to run all test suites at once, you can use:

```bash
task test
```

You can also run individual test suites like `task test/unit`, or bypass a
custom command by:

```bash
task phpunit -- --filter FooBarTest
```

## 🪤 Contribution

This is a personal project, I'm not expects any contributions to it. You can
send Pull Request with typo fixes and maybe other improvements, but don't expect
them to be merged. You better not to do that.

## 📬 Feedback

If you want to ask me about this project or its code base, feel free to
[contact me][contact-form].

[Task]: https://taskfile.dev/
[Taskfile.yml]: ./Taskfile.yml
[Yarn]: https://yarnpkg.com/
[drupal-system-requirements]: https://www.drupal.org/docs/system-requirements
[contact-form]: https://niklan.net/contact
