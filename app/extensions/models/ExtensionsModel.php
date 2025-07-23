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
    protected int  $id         = 0;
    protected bool $is_package = false;

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
        // data
        $this->filter(POST, [
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
        if (!Extensions::validate($this->data['extension_alias'])) {
            throw new Exception(sprintf(_t('The extension alias «%s» is invalid.'), $this->data['extension_alias']));
        }
        if (!$this->data['extension_name']) {
            $this->data['extension_name'] = ucfirst($this->data['extension_alias']);
        }

        // security
        $row = $this->db->safeFind("
		SELECT
		 default_credits,
		 default_license
		FROM `#__extensions_developers`
		WHERE id = ?
		AND is_protected = 0", $this->data['developer_id'])->fetch() or abort();

        if (!$this->data['extension_credits']) {
            $this->data['extension_credits'] = $row['default_credits'];
        }
        if (!$this->data['extension_license']) {
            $this->data['extension_license'] = $row['default_license'];
        }

        // extract
        $this->extract('id', 'is_package');
        $this->validatePackageId();

        // query
        if ($this->id) {
            $this->db->safeExec("UPDATE `#__extensions` SET ?? WHERE id = ?", $this->data, $this->id);
        } else {
            $this->data['extension_version'] = '0.1';
            $this->db->safeExec("INSERT INTO `#__extensions` (??) VALUES (??)", $this->data);
        }
    }

    /**
     * Status
     */
    public function status()
    {
        // data
        $this->filter(POST, [
            'id'     => 'id|array|required:abort',
            'status' => 'enum:extensions.extension_status|required:abort',
        ]);

        // query
        $rows = $this->db->safeFind("
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
		WHERE e.id IN ( ?.. )", $this->data['id'])->fetchAll() or abort();

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
        $status  = $this->data['status']->name;

        foreach ($servers as $server) {
            $response = $carrier->changeStatus(
                $server['webstore_url'],
                $server['webstore_token'],
                $server['extensions'],
                $status
            );

            if (!(int)$response) {
                echo $response;
                throw new Exception('Error!');
            }
        }

        // query
        $this->db->safeExec("UPDATE `#__extensions` SET status = ? WHERE id IN (?..)", $this->data['status'], $this->data['id']);
    }

    /**
     * Delete
     */
    public function delete()
    {
        // data
        $this->filter(POST, [
            'id'     => 'id|array|required:abort',
            'option' => 'array',
        ]);

        // query
        $rows = $this->db->safeFind("
		SELECT id, extension_alias
		FROM `#__extensions`
		WHERE id IN (?..)", $this->data['id'])->fetchAll();

        // files
        if (!empty($this->data['option']['files'])) {
            $this->removeFiles($rows);
        }

        // data
        if (!empty($this->data['option']['data'])) {
            $this->removeData($rows);
        }

        // database
        if (!empty($this->data['option']['db'])) {
            $this->removeDatabase($rows);
        }

        // delete
        $this->db->safeExec("DELETE FROM `#__extensions` WHERE id IN (?..)", $this->data['id']);
    }

    /**
     * Append
     */
    public function append()
    {
        // data
        $this->filter(POST, [
            'id'         => 'id|required:abort',
            'extensions' => 'id|array',
        ]);

        // query - remove
        $this->db->safeExec("
		UPDATE `#__extensions` 
		SET package_id = 0 
		WHERE package_id = ? 
		AND id NOT IN (?..)", $this->data['id'], $this->data['extensions']);

        // query - add
        $this->db->safeExec("
		UPDATE `#__extensions` 
		SET package_id = ? 
		WHERE id IN (?..)", $this->data['id'], $this->data['extensions']);
    }

    /**
     * Compile
     */
    public function compile()
    {
        // data
        $this->filter(POST, [
            'id'                    => 'id|array|required:abort',
            'get_install_package'   => 'bool',
            'package_name_format'   => 'int',
            'output'                => 'int',
            'plugins'               => 'array',
        ]);

        // compiler
        $compiler = new Compiler();
        $compiler->get_install_package = $this->data['get_install_package'];
        $compiler->package_name_format = $this->data['package_name_format'];
        $compiler->output              = $this->data['output'];
        $compiler->plugins             = array_keys($this->data['plugins']);

        foreach ($this->data['id'] as $package_id) {
            $compiler->compile($package_id);
        }
    }

    /**
     * DB History
     */
    public function dbHistory()
    {
        // data
        $this->filter(POST, [
            'id'         => 'id|required:abort',
            'db_history' => 'array',
        ]);

        // query
        $this->db->safeExec("UPDATE `#__extensions` SET ?? WHERE id = ?", [
            'db_history' => $this->getDbHistory()
        ], $this->data['id']);
    }

    /**
     * Update Readme
     */
    public function updateReadme()
    {
        // data
        $this->filter(POST, [
            'alias'  => 'text|required:abort',
            'readme' => '',
        ]);

        // vars
        $dir  = SYSTEM_STORAGE . sprintf('readme/%s/', $this->data['alias']);
        $file = $dir . 'README.html';

        is_dir($dir) or mkdir($dir);
        file_put_contents($file, $this->data['readme']);
    }

    /**
     * Distribute
     */
    public function distribute()
    {
        // data
        $this->filter(GET, ['id' => 'id|array:first|required:abort']);

        $messages = [];
        try {
            if (!set_time_limit(0)) { // set time limit
                throw new Exception('Error (time_limit)');
            }

            //
            $extension = $this->getExtensionData($this->data['id']) or abort();

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
                $this->db->safeExec("DROP $Type IF EXISTS `$Name`");
            }
        }
    }

    /**
     * Get
     */
    protected function validatePackageId()
    {
        if ($this->id) {
            $package_id = $this->db->safeFind("
			SELECT package_id 
			FROM `#__extensions`
			WHERE id = ?", $this->id)->fetchColumn();

            if ($package_id > 0) {
                return;
            }
        }

        $this->data['package_id'] = $this->is_package ? -1 : 0;
    }

    /**
     * Get
     */
    protected function getDbHistory()
    {
        $json = [];
        foreach ($this->data['db_history'] as $Type => $rows) {
            if ($Type == 'TABLE') {
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
        }

        if ($json) {
            return json_encode($json);
        }

        return '';
    }

    /**
     * Get
     */
    protected function getExtensionData(int $extension_id): array|false
    {
        return $this->db->safeFind("
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
