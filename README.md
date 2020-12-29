# FusionCMS CLI
CLI tool to install FusionCMS

- [Install](#Installation)
- [Update](#Update)
- [Usage](#Usage)


## Install

**Global Install** (recommended):

```bash
$ composer global require fusioncms/cli
```

**Local Install** (within your project folder):

```bash
$ composer require fusioncms/cli
```

### Global Usage
In order to run the `fusion` from anywhere you will first need to update your system's `$PATH` variable.

Find the location of your global composer `vendor/bin` directory:

```bash
$ composer config --list --global | grep -w home
> [home] /Users/{username}/.composer
```

Next, add the following to `~/.zshrc` or `~/.bashrc` (substituting `[home]` from above):

```bash
$ export PATH=[home]/vendor/bin:$PATH
```

## Update
Update to the latest release:

**Global**:

```bash
$ composer global update fusioncms/cli
```

**Local** (within your project folder):

```bash
$ composer update fusioncms/cli
```

## Usage
Download a fresh copy of FusionCMS (with [Laravel](https://laravel.com/)).

```bash
$ fusion new my-project
```

> This create a new folder `my-project` in the current directory.

