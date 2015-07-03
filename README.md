# CI3-Navigation-Library
Simple library for CodeIgniter v3 which outputs nav links in &lt;ul&gt; with support for active pages and multiple menus.

Author: **Daniel Waghorn**
[daniel-waghorn.com](https://www.daniel-waghorn.com)

## Structure
The folders in this repo are as follows:

`sql` contains an SQL dump which should be run against a database to create the required schema. *This is in MySQL format.*

`config` contains the config files that can be used for library. **Copy into your `application` directory.**

`libraries` contains the PHP files for the library. **Copy into your `application` directory.**

`models` contains required models for the library. **Copy into your `application` directory.**

## Usage
### Setup
1. Setup the database schema by importing the SQL file for navigation to create tables.
2. Copy the contents of `libraries` and `models` into their respective folders in your `application` directory.
3. Import the library whenever you need to output navigation; typically your header partial view using:
```php
$this->load->library('navigation');
```
You can also autoload it if you need navigation on every page.