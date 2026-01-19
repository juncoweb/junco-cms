<?php

use Junco\Settings\PluginLoader;
use Junco\Settings\PluginUpdater;

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

class Settings
{
    // vars
    protected string $key = '';
    protected string $alias;
    protected string $dirpath;
    protected string $valFile;
    protected string $dataPath;
    protected string $dataFile;
    protected array  $baseRow = [
        'label'            => '',
        'type'             => 'text',
        'group'            => 0,
        'ordering'         => 99,
        'value'            => null,
        'default_value'    => '',
        'help'             => '',
        'history'          => [],
        'autoload'         => 0,
        'translate'        => 0,
        'reload_on_change' => 0,
        'status'           => 1
    ];
    protected string $EOL = PHP_EOL;

    // const
    const MKDIR_MODE = 0775;

    /**
     * The construct
     *
     * @param string $key  The setting group key.
     */
    public function __construct(string $key = '', string $dirpath = '', bool $mkdir = false)
    {
        $this->setPath($dirpath);

        if ($key) {
            $this->setKey($key);
        }

        if ($mkdir) {
            $this->mkdir();
        }
    }

    /**
     * Set path to the dir where the settings file is located.
     * 
     * @param string $dirpath
     *
     * @return self
     */
    protected function setPath(string $dirpath = ''): self
    {
        $this->dirpath = ($dirpath ?: SYSTEM_SETPATH);

        return $this;
    }

    /**
     * Set
     * 
     * @param string $key  The setting group key.
     * 
     * @return self
     */
    public function setKey(string $key): self
    {
        $this->alias    = explode('-', $key, 2)[0];
        $this->key      = $key;
        $this->valFile  = $this->dirpath . $this->key . '.php';
        $this->dataPath = $this->dirpath . 'data/' . $this->alias . '/';
        $this->dataFile = $this->dataPath . $this->key . '.json';

        return $this;
    }

    /**
     * Security
     *
     * @return bool
     */
    public function security(): bool
    {
        return $this->key && db()->query("
		SELECT id
		FROM `#__extensions`
		WHERE extension_alias = ?", $this->alias)->fetchColumn();
    }

    /**
     * Get
     *
     * @param bool $run_plugin  Allows you to disable the execution of the settings plugin.
     * 
     * @return array
     */
    public function getData(bool $run_plugin = false): array
    {
        // data
        $data = [
            'title'        => '',
            'description'  => '',
            'descriptions' => [],
            'groups'       => [],
            'rows'         => [],
            'warning'      => [],
        ];

        if (!$this->readData($data)) {
            $data['warning'][] = _t('The specified file for saving the data was not found.');
        }

        if (!$this->readValues($data)) {
            $data['warning'][] = _t('The specified file for saving the settings has not been found.');
        }

        if ($run_plugin) {
            $this->runPlugin($data);
        }

        uasort($data['rows'], function ($a, $b) {
            $c = $a['group'] - $b['group'];

            if (!$c) {
                $c = $a['ordering'] - $b['ordering'];
            }

            return $c;
        });

        return $data;
    }

    /**
     * Get
     * 
     * @return array
     */
    public function getAllData(): array
    {
        $rows = [];

        foreach ($this->scandir($this->dataPath) as $file) {
            $info = pathinfo($file);

            if (
                $info['extension'] == 'json'
                && $this->isValidDataFilename($info['filename'])
                && ($data = $this->getJsonFromFile($this->dataPath . $file))
            ) {
                $rows[$info['filename']] = $data;
            }
        }

        return $rows;
    }

    /**
     * Get
     * 
     * @param string $file
     * 
     * @return bool
     */
    protected function isValidDataFilename($filename): bool
    {
        return $this->alias == $filename
            || $this->alias == substr($filename, 0, strlen($this->alias));
    }

    /**
     * Save configuration data in a json.
     *
     * @param array $data  The data to save.
     * 
     * @return void
     */
    public function save(array $data): void
    {
        foreach ($data['rows'] as $name => $row) {
            if ($row['autoload']) {
                unset($data['rows'][$name]['value']);
            }
        }

        unset($data['warning']);

        $this->write($this->dataFile, $data);
    }

    /**
     * Set values
     * 
     * @param array  $data     The key / value pair to configure.
     */
    public function set(array $data)
    {
        $this->write(
            $this->valFile,
            $this->createConfigFileContent($data)
        );
    }

    /**
     * Update
     *
     * @param array $rows
     *
     * @return void
     */
    public function update(array $rows = [])
    {
        // vars
        $data = $this->getData();

        foreach ($rows as $name => $value) {
            if (!isset($data['rows'][$name])) {
                throw new Exception(sprintf('%s: the variable «%s» to be set is not valid.', __METHOD__, $name));
            }

            $data['rows'][$name]['value'] = $value;
        }

        $this->set($data);
    }

    /**
     * Refresh
     * 
     * @throws Error
     */
    public function refresh()
    {
        // vars
        $data = [
            'descriptions' => [],
            'groups'       => [],
            'rows'         => [],
        ];

        if (!$this->readData($data)) {
            throw new Error(sprintf('The «%s» file storing the configuration metadata was not found.', $this->key));
        }

        $this->set($data);
    }

    /**
     * Has
     * 
     * @return bool
     */
    public function has()
    {
        return is_dir($this->dataPath);
    }

    /**
     * Delete
     */
    public function delete()
    {
        $this->remove($this->dataFile);
        $this->remove($this->dataPath . 'translate.' . $this->key . '.php');
        $this->remove($this->valFile);
    }

    /**
     * Delete All
     * 
     * @return bool
     */
    public function deleteAll()
    {
        (new Filesystem(''))->remove($this->dataPath);
    }

    /**
     * Translate.
     *
     * @param array $translate    A list with the texts to be translated.
     * @param array $is_frontend
     */
    public function translate(array $translate, bool $is_frontend = false)
    {
        if ($is_frontend) {
            (new LanguageHelper())->translate('settings.' . $this->key, $translate);
        } else {
            (new LanguageHelper())->translate('translate.' . $this->key, $translate, $this->dataPath);
        }
    }

    /**
     * Load current settings.
     * 
     * @return ?array
     */
    public function get(bool $force = false): ?array
    {
        if (!is_file($this->valFile)) {
            if (!$force) {
                return null;
            }

            $this->refresh();
        }

        $rows = settings_import($this->valFile);

        return is_array($rows) ? $rows : null;
    }

    /**
     * Read data
     */
    protected function readData(array &$data): bool
    {
        $curData = is_file($this->dataFile)
            ? $this->getJsonFromFile($this->dataFile)
            : false;

        if (!$curData) {
            return false;
        }

        $data = array_merge($data, $curData);

        if (!is_array($data['groups'])) {
            $data['groups'] = explode(',', $data['groups']);
        }

        if (!is_array($data['descriptions'])) {
            $data['descritions'] = explode(',', $data['descritions']);
        }

        if (is_array($data['rows'])) {
            foreach ($data['rows'] as $name => $row) {
                $data['rows'][$name] = is_array($row)
                    ? array_merge($this->baseRow, $row)
                    : $this->baseRow;
            }
        } else {
            $data['rows'] = [];
        }

        return true;
    }

    /**
     * Read values
     * 
     * @param array $data
     */
    protected function readValues(array &$data): bool
    {
        $rows = $this->get();

        if (!$rows) {
            return false;
        }

        foreach ($rows as $key => $value) {
            if (!isset($data['rows'][$key])) {
                $data['rows'][$key] = $this->baseRow;
                $data['rows'][$key]['label'] = $key;
            }

            $data['rows'][$key]['value']    = $value;
            $data['rows'][$key]['autoload'] = 1;
        }

        return true;
    }

    /**
     * Run
     * 
     * @param array $data
     */
    protected function runPlugin(array &$data): void
    {
        $plugin = Plugin::get('settings', 'load', str_replace('-', '.', $this->key));

        if (!$plugin) {
            return;
        }

        $loader = new PluginLoader($data['rows']);
        $plugin->run($loader);

        if ($loader->ok()) {
            $data['rows'] = $loader->fetchAll();
        } else {
            $data['warning'][] = _t('The plugin is adding keys, so it is ignored.');
        }
    }

    /**
     * Run
     * 
     * @param array $rows
     * 
     * @return array $rows
     */
    public function runUpdatePlugin(array $rows): array
    {
        $plugin = Plugin::get('settings', 'update', str_replace('-', '.', $this->key));

        if (!$plugin) {
            return $rows;
        }

        $loader = new PluginUpdater($rows);
        $plugin->run($loader);

        return $loader->fetchAll();
    }

    /**
     * Create
     * 
     * @param array $data
     * 
     * @return string
     */
    public function createConfigFileContent(array $data): string
    {
        // I extract the rows from the data
        $rows = [];
        foreach ($data['rows'] as $name => $row) {
            if ($row['autoload']) {
                $rows[$row['group']][$name] = $row['value'] ?? $row['default_value'];
            }
        }

        $bf    = '';
        foreach ($rows as $i => $group) {
            $title = $data['groups'][$i] ?? '';
            $bf .= $this->EOL
                . "\t" . '/**' . $this->EOL
                . "\t" . ' * ' . $title . $this->EOL
                . "\t" . ' */' . $this->EOL;

            foreach ($group as $key => $value) {
                $bf .= "\t" . "'" . $key . "'" . ' => ' . $this->var_export($value) . ',' . $this->EOL;
            }
        }

        return '<?php' . $this->EOL . $this->EOL
            . '/**' . $this->EOL
            . ' * ' . ucfirst($this->key) . $this->EOL
            . ' */' . $this->EOL . $this->EOL
            . 'return [' . $bf . '];' . $this->EOL;
    }

    /**
     * var_export
     */
    protected function var_export($var, $depth = 1): string
    {
        if (is_array($var)) {
            $tab = $this->EOL . str_repeat("\t", $depth);

            foreach ($var as $key => $value) {
                $var[$key] = $tab . "\t" . var_export($key, true) . ' => ' . $this->var_export($value, $depth + 1);
            }

            return '[' . implode(',', $var) . $tab . ']';
        }

        return var_export($var, true);
    }

    /**
     * Get
     * 
     * @param string $file
     * 
     * @return array|false
     */
    protected function getJsonFromFile(string $file): array|false
    {
        $data = file_get_contents($file);

        if ($data) {
            return json_decode($data, true);
        }

        return false;
    }

    /**
     * Scandir
     * 
     * @param string $dir
     * 
     * @return array
     */
    protected function scandir(string $dir): array
    {
        $cdir = is_readable($dir) ? scandir($dir) : false;

        return $cdir
            ? array_diff($cdir, ['.', '..'])
            : [];
    }

    /**
     * Write file
     *
     * @param string        $file
     * @param string|array  $data
     */
    protected function write(string $file, string|array $data)
    {
        if (is_array($data)) {
            $data = json_encode($data, JSON_PRETTY_PRINT);
        }

        if (false === file_put_contents($file, $data)) {
            throw new Exception(_t('An error occurred while trying to write the settings.'));
        }
    }

    /**
     * Remove
     */
    protected function remove($file)
    {
        is_file($file) and unlink($file);
    }

    /**
     * Make the data directory
     * 
     * @param string $setpath
     * 
     * @return string
     */
    protected function mkdir(): void
    {
        is_dir($this->dataPath)
            or mkdir($this->dataPath, self::MKDIR_MODE, true);
    }
}

/**
 * Import
 */
function settings_import(string $file)
{
    return include $file;
}
