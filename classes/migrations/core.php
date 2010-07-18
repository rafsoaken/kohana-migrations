<?php defined('SYSPATH') or die('No direct script access.');

class Migrations_Core {

    protected $config;
    protected $group;
    protected $use_table;
    protected $migrations_table = 'migrations';

    public function __construct( $group = 'default' )
    {
        $this->config = Kohana::config( 'migrations' );
        $this->group  = $group;
        $this->config['path'] = $this->config['path'][$group];
        $this->config['info'] = $this->config['path'] . $this->config['info'] . '/';
        $this->use_table = $this->config['use_migrations_table'];

        Database::instance( $this->group )->connect();
    }

    public function set_migrations_table( $name )
    {
        $this->migrations_table = $name;
    }

    public function get_schema_version ()
    {
        if( $this->use_table )
        {
            $last_migration = array_shift(DB::select('id')->from($this->migrations_table)
                ->limit(1)->order_by('id', 'desc')
                ->execute()->as_array());

            return (int)@$last_migration['id'];
        }

        if( ! is_dir( $this->config['path'] ) )
            mkdir( $this->config['path'] );

        if ( ! is_dir( $this->config['info'] ) )
            mkdir( $this->config['info'] );

        if ( ! file_exists( $this->config['info'] . 'version' ) ) {
            $fversion = fopen( $this->config['info'] . 'version', 'w' );
            fwrite( $fversion, '0' );
            fclose( $fversion );
            return 0;
        }
        else
        {
            $fversion = fopen( $this->config['info'] . 'version','r' );
            $version = fread( $fversion, 11 );
            fclose( $fversion );
            return $version;
        }
        return 0;
    }

    public function table_version_exists( $version )
    {
        return (bool) count(
                DB::select('id')->from($this->migrations_table)
                    ->limit(1)->where('id', '=', $version)
                    ->execute()->as_array()
            );
    }

    public function table_delete_versions( $version )
    {
        DB::delete($this->migrations_table)
            ->where('id', '>', $version-1)
            ->execute();
    }


    public function set_schema_version ( $version, $filename='' )
    {
        if( $this->use_table )
        {
            $this->table_delete_versions( $version );

            if ( $version === 0 )
                return;
                
            DB::insert(
                    $this->migrations_table,
                    array('id', 'filename')
                )
                ->values(array($version, $filename))
                ->execute();
            return;
        }

        $fversion = fopen( $this->config['info'] . 'version', 'w' );
        fwrite( $fversion, $version );
        fclose( $fversion );
    }

    public function last_schema_version ()
    {
        $migrations = $this->get_up_migrations();
        end( $migrations );
        return key( $migrations );
    }

    public function get_up_migrations ()
    {
        $migrations = glob( $this->config['path'] . '*UP.sql' );
        $actual_migrations = array();

        foreach ( $migrations as $i => $file )
        {
            $name = basename( $file, '.sql' );
            $matches = array();

            if ( preg_match( '/^(\d{3})_(\w+)$/', $name, $matches ) )
            	$actual_migrations[intval( $matches[1] )] = $file;
        }

        return $actual_migrations;
    }

    public function get_down_migrations ()
    {
        $migrations = glob( $this->config['path'] . '*DOWN.sql' );
        $actual_migrations = array();

        foreach ( $migrations as $i => $file )
        {
            $name = basename( $file, '.sql' );
            $matches = array();
            if ( preg_match( '/^(\d{3})_(\w+)$/', $name, $matches ) )
            	$actual_migrations[intval( $matches[1] )] = $file;
        }

        return $actual_migrations;
    }

    public function migrate ( &$controller, $from, $to )
    {
        if( $from < $to )
        {
            $migrations = $this->get_up_migrations();

            foreach( $migrations as $index => $migration )
            {
            	if( $index > $from and $index <= $to )
                {
            		try
                    {
            			$controller->out( $this->run_migration( $migration ) );
            			$this->set_schema_version( $index, $migration );
            		}
            		catch( Exception $e )
                    {
            			$controller->out( "Error running migration $index UP: " . $e->getMessage() . "\n" );
            			break;
            		}
        	    }
            }
        }
        else
        {
            $migrations = $this->get_down_migrations();
            $item = end( $migrations );

            while( false !== $item )
            {
            	$index = key( $migrations );

            	if( $index <= $from and $index > $to )
                {
            		try
                    {
            			$controller->out( $this->run_migration( $item ) );
            			$this->set_schema_version( $index - 1, $item );
            		}
            		catch( Exception $e )
                    {
            			$controller->out( "Error running migration $index DOWN: " . $e->getMessage() . "\n" );
            			break;
            		}
            	}

            	$item = prev( $migrations );
            }
        }
    }


    public function run_migration ( $file )
    {

        $contents = file_get_contents( $file );
        $queries = explode( ';', $contents );

        foreach( $queries as $query )
        {
            if( empty( $query ) ) { continue; }

            DB::query( NULL, $query )->execute();
        }

        return "Migrated: " . basename( $file ) . "\n";
    }

}
