C2iS PHP CodeSniffer Coding Standard
====================================

A code standard to check against the C2iS coding standards, inspired by the [Symfony2 coding standards](https://github.com/opensky/Symfony2-coding-standard/)

Installation
------------

Install phpcs:

```bash
$ pear install PHP_CodeSniffer
```

Find your PEAR directory:

```bash
$ pear config-show | grep php_dir
```

Copy, symlink or check out this repo to a folder called **Symfony2** inside the phpcs `Standards` directory:

```bash
$ cd /path/to/pear/PHP/CodeSniffer/Standards
$ git clone git://github.com/opensky/Symfony2-coding-standard.git Symfony2
```

Copy, symlink or check out this repo to a folder called **C2iS** inside the phpcs `Standards` directory:

```bash
$ cd /path/to/pear/PHP/CodeSniffer/Standards
$ git clone git://github.com/c2is/C2iS-coding-standards.git C2iS
```

Set C2iS as your default coding standard:

```bash
$ phpcs --config-set default_standard C2iS
```

Profit!

```bash
$ cd /path/to/your/project
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