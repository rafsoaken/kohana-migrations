<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Migrations_Core extends Controller {

    protected $separator;
    public $allow_from_controller = NULL;
    public $allow_from_action = NULL;

    public function __construct (Kohana_Request $request)
    {
        parent::__construct($request);

        $this->separator = str_pad("\n", 68, '=', STR_PAD_LEFT);

        // Allow command line access or invocation from allowed controller (eg. as HMVC request)
        if( Kohana::$is_cli
            OR
            (
              !is_null($this->allow_from_controller) //there is a controller from which we allow invocation
              AND
              (
               $this->allow_from_controller == @Request::$instance->controller  //this controller comes from the main request
               AND  //allows for just one action of this controller or all actions (if $allow_from_action is NULL)
               (is_null($this->allow_from_action) OR $this->allow_from_controller == @Request::$instance->action)
              )
            )
          )
        {
            // ok, we can continue
        }
        else
        {
            die('No access!');
        }

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
        $this->out();
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
