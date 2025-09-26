<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;
use Junco\Extensions\Components;
use Junco\Extensions\Extensions;
use Junco\Extensions\Updater\Carrier;
use Junco\Extensions\XData\XDataManager;
use Junco\Extensions\Compiler\Compiler;
use Junco\Extensions\Enum\ExtensionStatus;

class ExtensionsModel extends Model
{
    // vars
    protected $db;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = db();
    }

    /**
     * save
     */
    public function save()
    {
        $data = $this->filter(POST, [
            'id'                 => 'id',
            'developer_id'       => 'id|required',
            'extension_alias'    => 'required',
            'extension_name'     => 'text',
            'extension_credits'  => 'text',
            'extension_license'  => 'text',
            'extension_abstract' => 'text',
            'extension_require'  => 'text',
            'is_package'         => 'bool',
        ]);

        // validate
        if (!Extensions::validate($data['extension_alias'])) {
            return $this->unprocessable(sprintf(_t('The extension alias «%s» is invalid.'), $data['extension_alias']));
        }
        if (!$data['extension_name']) {
            $data['extension_name'] = ucfirst($data['extension_alias']);
        }

        // security
        $developer = $this->getDeveloperData($data['developer_id']) or abort();

        if (!$data['extension_credits']) {
            $data['extension_credits'] = $developer['default_credits'];
        }
        if (!$data['extension_license']) {
            $data['extension_license'] = $developer['default_license'];
        }

        // slice
        $extension_id = $this->slice($data, 'id');
        $is_package   = $this->slice($data, 'is_package');

        $data['package_id'] = $this->getValidPackageId($extension_id, $is_package);

        // query
        if ($extension_id) {
            $this->db->exec("UPDATE `#__extensions` SET ?? WHERE id = ?", $data, $extension_id);
        } else {
            $data['extension_version'] = '0.1';
            $this->db->exec("INSERT INTO `#__extensions` (??) VALUES (??)", $data);
        }
    }

    /**
     * Status
     */
    public function status()
    {
        $data = $this->filter(POST, [
            'id'     => 'id|array|required:abort',
            'status' => 'enum:extensions.extension_status|required:abort',
        ]);

        // query
        $rows = $this->db->query("
		SELECT
		 e.status ,
		 e.extension_alias ,
		 e.extension_name ,
		 e.developer_id ,
		 d.webstore_url ,
		 d.webstore_token ,
		 d.is_protected
		FROM `#__extensions` e
		LEFT JOIN `#__extensions_developers` d ON ( e.developer_id = d.id )
		WHERE e.id IN ( ?.. )", $data['id'])->fetchAll() or abort();

        $servers = [];
        foreach ($rows as $row) {
            // security
            $row['is_protected'] and abort();

            if ($row['webstore_token']) {
                if (!isset($servers[$row['developer_id']])) {
                    $servers[$row['developer_id']] = [
                        'webstore_url'   => $row['webstore_url'],
                        'webstore_token' => $row['webstore_token'],
                        'extensions'     => []
                    ];
                }
                $servers[$row['developer_id']]['extensions'][] = $row['extension_alias'];
            }
        }

        $carrier = new Carrier;
        $status  = $data['status']->name;

        foreach ($servers as $server) {
            $response = $carrier->changeStatus(
                $server['webstore_url'],
                $server['webstore_token'],
                $server['extensions'],
                $status
            );

            if (!(int)$response) {
                echo $response;
                return $this->unprocessable('Error!');
            }
        }

        // query
        $this->db->exec("UPDATE `#__extensions` SET status = ? WHERE id IN (?..)", $data['status'], $data['id']);
    }

    /**
     * Delete
     */
    public function delete()
    {
        $data = $this->filter(POST, [
            'id'     => 'id|array|required:abort',
            'option' => 'array',
        ]);

        // query
        $rows = $this->db->query("
		SELECT id, extension_alias
		FROM `#__extensions`
		WHERE id IN (?..)", $data['id'])->fetchAll();

        // files
        if (!empty($data['option']['files'])) {
            $this->removeFiles($rows);
        }

        // data
        if (!empty($data['option']['data'])) {
            $this->removeData($rows);
        }

        // database
        if (!empty($data['option']['db'])) {
            $this->removeDatabase($rows);
        }

        // delete
        $this->db->exec("DELETE FROM `#__extensions` WHERE id IN (?..)", $data['id']);
    }

    /**
     * Append
     */
    public function append()
    {
        $data = $this->filter(POST, [
            'id'         => 'id|required:abort',
            'extensions' => 'id|array',
        ]);

        // query - remove
        $this->db->exec("
		UPDATE `#__extensions` 
		SET package_id = 0 
		WHERE package_id = ? 
		AND id NOT IN (?..)", $data['id'], $data['extensions']);

        // query - add
        $this->db->exec("
		UPDATE `#__extensions` 
		SET package_id = ? 
		WHERE id IN (?..)", $data['id'], $data['extensions']);
    }

    /**
     * Compile
     */
    public function compile()
    {
        $data = $this->filter(POST, [
            'id'                    => 'id|array|required:abort',
            'get_install_package'   => 'bool',
            'package_name_format'   => 'int',
            'output'                => 'int',
            'plugins'               => 'array',
        ]);

        // compiler
        $compiler = new Compiler();
        $compiler->get_install_package = $data['get_install_package'];
        $compiler->package_name_format = $data['package_name_format'];
        $compiler->output              = $data['output'];
        $compiler->plugins             = array_keys($data['plugins']);

        foreach ($data['id'] as $package_id) {
            $compiler->compile($package_id);
        }
    }

    /**
     * DB History
     */
    public function dbHistory()
    {
        $data = $this->filter(POST, [
            'id'         => 'id|required:abort',
            'db_history' => 'array',
        ]);

        $data['db_history'] = $this->getDbHistory($data['db_history']);

        // query
        $this->db->exec("UPDATE `#__extensions` SET db_history = ? WHERE id = ?", $data['db_history'], $data['id']);
    }

    /**
     * Update Readme
     */
    public function updateReadme()
    {
        $data = $this->filter(POST, [
            'alias'  => 'text|required:abort',
            'readme' => '',
        ]);

        // vars
        $dir  = SYSTEM_STORAGE . sprintf('readme/%s/', $data['alias']);
        $file = $dir . 'README.html';

        is_dir($dir) or mkdir($dir);
        file_put_contents($file, $data['readme']);
    }

    /**
     * Distribute
     * 
     * @throws Exception
     * 
     * @return array
     */
    public function distribute(): array
    {
        $data = $this->filter(GET, ['id' => 'id|array:first|required:abort']);

        $messages = [];
        try {
            if (!set_time_limit(0)) { // set time limit
                throw new Exception('Error (time_limit)');
            }

            //
            $extension = $this->getExtensionData($data['id']) or abort();

            if (!$extension['webstore_token']) {
                throw new Exception(_t('The distribution system requires a token.'));
            }

            $messages[] = sprintf(_t('Uploading «%s»...'), $extension['name']);

            // vars
            $file = sprintf('%s_%s.zip', $extension['alias'], $extension['version']);
            $messages[] = (new Carrier)->distribute(
                $extension['webstore_url'],
                $extension['webstore_token'],
                $file
            );
        } catch (Exception $e) {
            $messages[] = sprintf('%d - %s', $e->getCode(), $e->getMessage());
        }

        return ['messages' => $messages];
    }

    /**
     * Remove
     */
    protected function removeFiles(array $rows)
    {
        $components = new Components();
        $fs = new Filesystem();

        foreach ($rows as $row) {
            foreach ($components->getDirectories($row['extension_alias']) as $dir) {
                $fs->remove($dir);
            }
        }
    }

    /**
     * Remove
     */
    protected function removeData(array $rows)
    {
        $xdm = new XDataManager;

        foreach ($rows as $row) {
            $has = $xdm->find($row['id'], $row['extension_alias']);

            if ($has) {
                $xdm->add($has, $row['extension_alias'], $row['id']);
            }
        }

        $xdm->exec('delete');
    }

    /**
     * Remove
     */
    protected function removeDatabase(array $rows)
    {
        $drop = [];

        foreach ($rows as $row) {
            $queries = Extensions::getQueries($row['extension_alias'], true);

            foreach ($queries as $query) {
                $drop[$query['Type']][] = $query['Name'];
            }
        }

        foreach ($drop as $Type => $Names) {
            foreach ($Names as $Name) {
                $this->db->exec("DROP $Type IF EXISTS `$Name`");
            }
        }
    }

    /**
     * Get
     */
    protected function getDeveloperData(int $developer_id): array|false
    {
        return $this->db->query("
		SELECT
         id ,
		 default_credits,
		 default_license
		FROM `#__extensions_developers`
		WHERE id = ?
		AND is_protected = 0", $developer_id)->fetch();
    }

    /**
     * Get
     */
    protected function getValidPackageId(int $extension_id, bool $is_package): int
    {
        if ($extension_id) {
            $package_id = $this->db->query("
			SELECT package_id 
			FROM `#__extensions`
			WHERE id = ?", $extension_id)->fetchColumn();

            if ($package_id > 0) {
                return $package_id;
            }
        }

        return $is_package ? -1 : 0;
    }

    /**
     * Get
     */
    protected function getDbHistory(array $db_history): string
    {
        $json = [];

        foreach ($db_history as $Type => $rows) {
            if ($Type != 'TABLE') {
                continue;
            }

            foreach ($rows as $Name => $Table) {
                if ($Table['History']) {
                    $json[$Type][$Name]['History'] = array_map('trim', explode(',', $Table['History']));
                }

                foreach ($Table['Fields'] as $Field_Name => $Field) {
                    if ($Field['History']) {
                        $json[$Type][$Name]['Fields'][$Field_Name]['History'] = array_map('trim', explode(',', $Field['History']));
                    }
                }
            }
        }

        return $json
            ? json_encode($json)
            : '';
    }

    /**
     * Get
     */
    protected function getExtensionData(int $extension_id): array|false
    {
        return $this->db->query("
        SELECT
         e.extension_alias AS alias,
         e.extension_name AS name,
         e.extension_version AS version,
         d.webstore_url ,
         d.webstore_token
        FROM `#__extensions` e
        LEFT JOIN `#__extensions_developers` d ON (e.developer_id = d.id)
        WHERE e.id = ?
        AND e.status IN ( ?.. )", $extension_id, ExtensionStatus::getActives())->fetch();
    }
}
