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
    * Subdirectory to store meta-information about the state of the migrations.
    */
	'info' =>  '.info'
);
