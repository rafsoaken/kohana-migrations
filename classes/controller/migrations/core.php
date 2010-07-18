<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Migrations_Core extends Controller {

    protected $separator;

    public function __construct (Kohana_Request $request)
    {
        parent::__construct($request);

        $this->separator = str_pad("\n", 68, '=', STR_PAD_LEFT);

        // Command line access ONLY
        if( ! Kohana::$is_cli ) { die('No web access'); }

        $this->stdout = fopen( 'php://stdout', 'w' );
        $this->out( "\n=======================[ Kohana Migrations ]=======================\n" );
        $this->migrations = new Migrations();
    }

    public function __destruct ()
    {
        fclose( $this->stdout );
    }

    public function out ( $line = "\n" )
    {
        fwrite( $this->stdout, $line );
        fflush( $this->stdout );
    }

    public function action_index ()
    {
        $this->_print_status();
        $this->out();
    }

    public function action_up ( $version = null )
    {
        $this->_migrate( $version );
    }

    public function action_down ( $version = null )
    {
        $this->_migrate( $version, true );
    }

    protected function _migrate ( $version, $down = false )
    {
        if( is_null( $version ) )
        {
            $version = $this->migrations->last_schema_version();
        }

        $current_version = $this->migrations->get_schema_version();
        $last_version = $this->migrations->last_schema_version();

        $direction = ( $down ) ? 'DOWN' : 'UP';

        $this->_print_status( 'Migrate' );

        $out =  "Requested Migration: $version\n" .
                "Migrating: $direction\n" .
                $this->separator;

        $this->out( $out );

        if( $down )
        {
            if( $version >= $current_version )
            {
                $this->out( "** Nothing To Do!\n" );
            }
            else
            {
                $this->migrations->migrate( $this, $current_version, $version );
            }
        }
        else
        {
            if( $version <= $current_version )
            {
                $this->out( "** Nothing To Do!\n" );
            }
            else
            {
                $this->migrations->migrate( $this, $current_version, $version );
            }
        }

        $this->_print_status();
    }

    protected function _print_status ()
    {
        $current_version = $this->migrations->get_schema_version();
        $last_version = $this->migrations->last_schema_version();
        $out =  "Current Migration: $current_version\n" .
                "Latest Migration : $last_version\n" .
                $this->separator;

        $this->out( $out );
    }

}
