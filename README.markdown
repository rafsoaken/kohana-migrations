# About

This Kohana module provides simple migrations from the command line for SQL compliant databases.

The versioning information can be file or table based.


# Installation (for Kohana v3.x)

Put the migrations module in your modules path (after downloading as archive or cloning via git / git submodules -> see [Kohana and git](http://kohanaframework.org/guide/tutorials.git))

Enable it in your bootstrap.php by adding a line to the array in the Kohana::modules() call:

    Kohana::modules(array(
        ...
        'migrations'  => MODPATH.'migrations',           // Kohana v3.x migrations module  
    )); 


# Using

Create a folder named "migrations" in your application folder.

Put valid SQL files in that folder, following the naming patterns: 001_ANYTHING_HERE_UP.sql & 001_ANYTHING_HERE_DOWN.sql

** DO NOT START WITH 000 but 001, or this migration will not work (The base version the migrations module assumes to be in is 0. Thus it will neither execute UP nor DOWN migrations to reach this version.)

For example:

	001_Auth_DOWN.sql
	001_Auth_UP.sql
	002_Users_DOWN.sql
	002_Users_UP.sql
	003_Island_DOWN.sql
	003_Island_UP.sql
	004_Pages_DOWN.sql
	004_Pages_UP.sql

Then you can run them from the command line:

## Status

	jmhobbs@katya:/var/www/qaargh$ php5 index.php --uri=migrations

	=======================[ Kohana Migrations ]=======================

	    Current Migration: 7
	     Latest Migration: 7

	===================================================================


## Up

	jmhobbs@katya:/var/www/qaargh$ php5 index.php --uri=migrations/up/3

	=======================[ Kohana Migrations ]=======================

	   Current Migration: 0
	    Latest Migration: 7

	===================================================================

	  Requested Migration: 3
	            Migrating: UP

	===================================================================

	Migrated: 001_Auth_UP.sql
	Migrated: 002_Users_UP.sql
	Migrated: 003_Island_UP.sql

	===================================================================

	    Current Migration: 3
	     Latest Migration: 7

	===================================================================


## Down

	jmhobbs@katya:/var/www/qaargh$ php5 index.php --uri=migrations/down/0

	=======================[ Kohana Migrations ]=======================

	    Current Migration: 3
	     Latest Migration: 7

	===================================================================

	  Requested Migration: 0
	            Migrating: DOWN

	===================================================================

	Migrated: 003_Island_DOWN.sql
	Migrated: 002_Users_DOWN.sql
	Migrated: 001_Auth_DOWN.sql

	===================================================================

	    Current Migration: 0
      Latest Migration: 7

	===================================================================

# Migrations Table

If you want to use a table to track the version changes to your database, you can!

Just specify the key 'use_migrations_table' as TRUE in the migrations config file and it will look for a table named 'migrations' in your database.

The structure of this table is as follows:

    CREATE TABLE IF NOT EXISTS `migrations` (
        `id` int(11) NOT NULL,
        `filename` varchar(255) COLLATE utf8_swedish_ci NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;


# License

	Copyright (c) 2010 John Hobbs

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in
	all copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.

# Inspiration

Inspiration and base code from https://code.google.com/p/kohana-migrations/
