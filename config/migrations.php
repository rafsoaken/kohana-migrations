<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(
    /**
    * Path to your migrations folder.
    * Typically, it will be within your application path.
    * -> Writing permission is required within the migrations path.
    *
    * Paths are organized by database groups
    */
	'path' => array (
        'default' => APPPATH . 'migrations/',
    ),


    /**
    * If true use a table to store the version information instead of flat file
    */
    'use_migrations_table' => True,

    /**
    * Subdirectory to store meta-information about the state of the migrations (if file based).
    */
	'info' =>  '.info'
);
