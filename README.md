# CI3-Navigation-Library
Simple library for CodeIgniter v3 which outputs nav links in &lt;ul&gt; with support for active pages and multiple menus.

The library makes use of an underlying database for storing menus and the items within these menus. This provides a solid and familiar foundation on which front end management tools can be built on to allow users to manage the menus.

Author: **Daniel Waghorn** 
[daniel-waghorn.com](https://www.daniel-waghorn.com)

## Licencing
This project is released in the public domain under the [MIT Licence](http://opensource.org/licenses/MIT).

## Structure
The folders in this repo are as follows:

`sql` contains an SQL dump which should be run against a database to create the required schema. *This is in MySQL format.*

`config` contains the config files that can be used for library. **Copy into your `application` directory.**

`libraries` contains the PHP files for the library. **Copy into your `application` directory.**

`models` contains required models for the library. **Copy into your `application` directory.**

## Usage
### Setup
1. Setup the database schema by importing the SQL file for navigation to create tables.
2. Copy the contents of `config`, `libraries` and `models` into their respective folders in your `application` directory.
3. Import the library whenever you need to output navigation and invoke generation by following the steps below at [Using the Library](#using-the-library)


### Database Structure
The tables used by the database should be in your primary CodeIgniter database and consist of `ci-nav-menus`, `ci-nav-items` and `ci-nav-inmenu`.

The `ci-nav-menus` table links `MenuID`s to a human readable `MenuName`. For example if I needed a user menu and an admin menu in my application this table would consist of two rows e.g: 

| MenuID | MenuName            |
| ------ | ------------------- |
| 1      | users               |
| 2      | admin               |


A table named `ci-nav-items` holds individual navigation items or links and their related data. This promotes reuse of items across multiple menus.

* `ItemID` is the primary key which is used to link items into menus. 
* `ItemName` is merely a name which makes the item easily recognisable in the database tables. 
* `ItemHumanName` is the name which is rendered in the output for the item. This can include HTML if you want to include e.g. an icon font for each item.
* `ItemLink` is the URL that links to the item's destination. This should be relative if linking internally; for instance if your login url is `http://mysite.com/login` the value in this field would simply be `login`. If linking externally then include a fully qualified url such as `http://externalsite.com/page`.
* `ParentItem` links nav items to another nav item which would be its parent. If a nav item has other nav items referring to its `ItemID` in their respective `ParentItem` field then a submenu will be rendered underneath this item.
** N.B. This field should be [NULL] by default.

Finally a table named `ci-nav-inmenu` links nav items to menus. This table contains three columns: 
* `MenuID` which references the `MenuID` for a given menu in `ci-nav-menus`. 
* `ItemID` which references the `ItemID` for the item to link into the menu as in `ci-nav-items`
* `LinkWeight` which assigns a weight to a particular relation. Larger `LinkWeight`s sink to the bottom/end of menu, lower `LinkWeight`s float to the top/start of menu.

For example to place a nav item with `ItemID` = 2 into the menu with `MenuID` = 1 the `ci-nav-inmenu` table should contain the following row:

| MenuID | ItemID | LinkWeight |
| ------ | ------ | ---------- |
| 1      | 2      | 50         |

I assigned `LinkWeight` 50 since on a scale of 1 to 100 this would be neutral, and leaves plenty of options to arrange links either side.

### Navigation Configurations
This library supports interchangeable config files which control the markup used when rendering the navigation. These files reside in the `config` folder, where the default config file which is loaded is `navigation.php`.

This file contains PHPdoc explaining each of the settings and what they control.

To specify an alternative configuration to load simply supply an associative array as a second parameter when loading the library as below.

```php
    $this->load->library('navigation',array('config' => 'navigation_foundation'));
```

The associative array should contain `'config' => 'config_file_name'`. ** Make sure not to include the .php extension when specifying `config_file_name`.

### Using the Library
Using the library is simple; in each controller it's easiest to load it in the constructor for that controller like so:

```php
    $this->load->library('navigation');
```

You can also alternatively autoload it or implement it in your `MY_Controller` superclass constructor.

Whenever you need to output navigation it's normally easiest to have CodeIgniter return the markup rather than render it. This way you can pass the markup to your views via `$data` and output it exactly where you need it.

An example of this would be something like: 

```php
    $data['navigation'] = $this->navigation->generateNav_fromName('user');
```

Where this would store the markup for the 'user' menu into `$data['navigation']`.

This data is then output in the view by placing `<?php echo $navigation ?>` wherever you want the markup to be placed.

### Function Description

The file at `libraries/Navigation.php` contains a few functions which you will need to invoke in order to create navigation.

This file contains PHPdoc explaining each along with parameters and return values.

#### Generate Navigation from Menu Name
This function takes the name as specified in the `ci-nav-menus` table for `MenuName` and returns the menu with the linked items.
Usage:
```php
   $data['navigation'] = $this->navigation->generateNav_fromName('public');
```

This would generate the markup for the menu with name 'public'.

#### Generate Navigation from Menu ID
This function takes the id as specified in the `ci-nav-menus` table for `MenuID` and returns the menu with the linked items.
Usage:
```php
   $data['navigation'] = $this->navigation->generateNav_fromID(2);
```

This would generate the markup for the menu with ID 2.

#### Generate Role Based Nav
This function allows you to generate a menu based on a user's group association if your project is using [Ben Edmund's Ion Auth](http://benedmunds.com/ion_auth/ "Ion Auth Homepage") library.
Define user groups in Ion Auth, then edit `generateRoleBasedNav` to return the menu specific to the context.

The default method contains a few examples for integration as well as support for public, user and admin menus out of the box.

Usage:
```php
	$data['navigation'] = $this->navigation->generateRoleBasedNav();
```


