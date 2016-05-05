# git-wrapper

A PHP (read-only) wrapper for the Git command line utility.

[![Latest Stable Version](https://img.shields.io/packagist/v/ichhabrecht/git-wrapper.svg)](https://packagist.org/packages/ichhabrecht/git-wrapper)

## Installation

It's recommended that you use [Composer](https://getcomposer.org/) to install the git-wrapper.

```bash
$ composer require ichhabrecht/git-wrapper
```

## Usage

**Clone a repository**

```php
$gitWrapper = new IchHabRecht\GitWrapper\GitWrapper();

$gitRepository = $gitWrapper->cloneRepository('https://github.com/IchHabRecht/git-wrapper.git', __DIR__ . '/git-wrapper');
```

**Get working copy**

```php
$gitWrapper = new IchHabRecht\GitWrapper\GitWrapper();

$gitRepository = $gitWrapper->getRepository(__DIR__ . '/git-wrapper');
$gitRepository->fetch();
$gitRepository->pull();
```
