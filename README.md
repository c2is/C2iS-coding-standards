C2iS PHP CodeSniffer Coding Standard
====================================

A code standard to check against the C2iS coding standards, inspired by the [Symfony2 coding standards](https://github.com/opensky/Symfony2-coding-standard/)

Installation
------------

1. Install phpcs:

```bash
$ pear install PHP_CodeSniffer
```

2. Find your PEAR directory:

```bash
$ pear config-show | grep php_dir
```

3. Copy, symlink or check out this repo to a folder called C2iS inside the
   phpcs `Standards` directory:

```bash
$ cd /path/to/pear/PHP/CodeSniffer/Standards
$ git clone git@github.com:c2is/C2iS-coding-standards.git C2iS
```

4. Set C2iS as your default coding standard:

```bash
$ phpcs --config-set default_standard C2iS
```

5. Profit!

```bash
$ cd /path/to/c2is/project
$ phpcs app src
$ phpcs path/to/my/file.php
```

You can also use our internal task:

```bash
$ cd /path/to/c2is/project
$ ./console tests:sniff
```

Contributing
------------

If you do contribute code to these sniffs, please make sure it conforms to the PEAR
coding standard.

To check the coding standard, run from the C2iS-coding-standard source root:

```bash
$ phpcs --standard=PEAR . -n
```