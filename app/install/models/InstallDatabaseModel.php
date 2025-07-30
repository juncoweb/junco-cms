<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class InstallDatabaseModel extends Model
{
    /**
     * Get Data
     */
    public function getData()
    {
        // vars
        $data = [
            'can_connect' => Install::dbCanConnect()
        ];

        if (!$data['can_connect']) {
            $config     = (new Config)->get('database');
            $use_pdo = false;

            if ($config['database.adapter'] == 'pdo') {
                $config['database.adapter'] = (new Config)->get('database-pdo.adapter');
                $use_pdo = true;
            }
            $json = file_get_contents(SYSTEM_ABSPATH . 'app/install/collations/collations.json');
            $data += [
                'collations' => json_decode($json, true),
                'values' => [
                    'db_adapter'    => $config['database.adapter'],
                    'use_pdo'        => $use_pdo,
                    'db_server'        => $config['database.server'],
                    'db_username'    => $config['database.username'],
                    'db_password'    => $config['database.password'],
                    'db_port'        => $config['database.port'],
                    'db_database'    => $config['database.database'],
                    'db_collation'    => $config['database.collation'],
                    'db_prefix'        => $config['database.prefix'],
                ],
            ];
        }

        return $data;
    }

    /**
     * Save
     */
    public function save()
    {
        // data
        $this->filter(POST, [
            'db_adapter'   => '',
            'use_pdo'      => '',
            'db_server'    => '',
            'db_username'  => '',
            'db_password'  => '',
            'db_port'      => 'id',
            'db_database'  => '',
            'db_prefix'    => '',
            'db_collation' => '',
            'email'        => 'email',
        ]);

        // validate
        if (!$this->data['db_server']) {
            return $this->unprocessable(_t('Please, fill in the host.'));
        }
        if (!$this->data['db_username']) {
            return $this->unprocessable(_t('Please, fill in the user.'));
        }
        if (!$this->data['db_database']) {
            return $this->unprocessable(_t('Please, fill in the name of the database.'));
        }

        // vars
        $this->data['db_charset'] = substr($this->data['db_collation'], 0, strpos($this->data['db_collation'], '_'));
        if ($this->data['db_prefix'] && substr($this->data['db_prefix'], -1) != '_') {
            $this->data['db_prefix'] .= '_';
        }

        if ($this->data['use_pdo']) {
            $pdo_adapter = $this->data['db_adapter'];
            $this->data['db_adapter'] = 'pdo';
        } else {
            $pdo_adapter = '';
        }

        if (!$this->connect()) {
            //return $this->unprocessable($e->getMessage());
            return $this->unprocessable(_t('The database could not be found or created.'));
        }


        // update settings
        (new Settings('database'))->update([
            'adapter'   => $this->data['db_adapter'],
            'server'    => $this->data['db_server'],
            'username'  => $this->data['db_username'],
            'password'  => $this->data['db_password'],
            'port'      => $this->data['db_port'],
            'database'  => $this->data['db_database'],
            'prefix'    => $this->data['db_prefix'],
            'collation' => $this->data['db_collation'],
            'charset'   => $this->data['db_charset'],
        ]);
        if ($pdo_adapter) {
            (new Settings('database-pdo'))->update([
                'pdo_adapter' => $pdo_adapter,
            ]);
        }
    }

    /**
     * Connect
     */
    protected function connect(): Junco\Database\Adapter\AdapterInterface|null
    {
        switch ($this->data['db_adapter']) {
            case 'pdo':
                $class = Junco\Database\Adapter\PdoAdapter::class;
                break;
            case 'pgsql':
                $class = Junco\Database\Adapter\PgsqlAdapter::class;
                break;
            default:
                $class = Junco\Database\Adapter\MysqlAdapter::class;
                break;
        }

        try {
            $db = new $class([
                'database.adapter'  => 'mysql',
                'database.server'   => $this->data['db_server'],
                'database.username' => $this->data['db_username'],
                'database.password' => $this->data['db_password'],
                'database.database' => $this->data['db_database'],
                'database.port'     => $this->data['db_port'],
                'database.charset'  => $this->data['db_charset'],
            ]);
        } catch (Throwable $e) {
            return null;
        }

        return $db;


        /*
		// create database
		$db->getSchema()->database()->create(
			$this->data['db_database'],
			$this->data['db_charset'],
			$this->data['db_collation']
		); */
    }
}
