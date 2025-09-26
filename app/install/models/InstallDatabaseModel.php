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
        $data = $this->filter(POST, [
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
        if (!$data['db_server']) {
            return $this->unprocessable(_t('Please, fill in the host.'));
        }
        if (!$data['db_username']) {
            return $this->unprocessable(_t('Please, fill in the user.'));
        }
        if (!$data['db_database']) {
            return $this->unprocessable(_t('Please, fill in the name of the database.'));
        }

        // vars
        $data['db_charset'] = substr($data['db_collation'], 0, strpos($data['db_collation'], '_'));
        if ($data['db_prefix'] && substr($data['db_prefix'], -1) != '_') {
            $data['db_prefix'] .= '_';
        }

        if ($data['use_pdo']) {
            $pdo_adapter = $data['db_adapter'];
            $data['db_adapter'] = 'pdo';
        } else {
            $pdo_adapter = '';
        }

        if (!$this->connect($data)) {
            //return $this->unprocessable($e->getMessage());
            return $this->unprocessable(_t('The database could not be found or created.'));
        }


        // update settings
        (new Settings('database'))->update([
            'adapter'   => $data['db_adapter'],
            'server'    => $data['db_server'],
            'username'  => $data['db_username'],
            'password'  => $data['db_password'],
            'port'      => $data['db_port'],
            'database'  => $data['db_database'],
            'prefix'    => $data['db_prefix'],
            'collation' => $data['db_collation'],
            'charset'   => $data['db_charset'],
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
    protected function connect(array $data): Junco\Database\Adapter\AdapterInterface|null
    {
        switch ($data['db_adapter']) {
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
                'database.server'   => $data['db_server'],
                'database.username' => $data['db_username'],
                'database.password' => $data['db_password'],
                'database.database' => $data['db_database'],
                'database.port'     => $data['db_port'],
                'database.charset'  => $data['db_charset'],
            ]);
        } catch (Throwable $e) {
            return null;
        }

        return $db;


        /*
		// create database
		$db->getSchema()->database()->create(
			$data['db_database'],
			$data['db_charset'],
			$data['db_collation']
		); */
    }
}
