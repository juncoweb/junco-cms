<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class SettingsImporter extends Settings
{
    /**
     * Import settings from a source.
     * 
     * @param string $dirpath  The path to the source settings.
     * 
     * @return int The number of files imported.
     */
    public function import(string $dirpath): int
    {
        $rows = (new Settings($this->key, $dirpath))->getAllData();

        if (!$rows) {
            return 0;
        }

        $this->mkdir();

        foreach ($rows as $key => $data) {
            if ($this->key !== $key) {
                $this->setKey($key);
            }

            $this->mergeWithCurrentData($data);
            $this->save($data);
            $this->set($data);
        }

        return count($rows);
    }

    /**
     * Import settings from buffer.
     * 
     * @param string $buffer  The buffer of settings.
     */
    public function importFromBuffer(string $buffer)
    {
        $data = json_decode($buffer, true);

        if ($data) {
            $this->mergeWithCurrentData($data);
            $this->save($data);
            $this->set($data);
        }
    }

    /**
     * Merge
     * 
     * @param array $data
     * 
     * @return bool
     */
    protected function mergeWithCurrentData(array &$data): bool
    {
        if (is_file($this->valFile)) {
            $curData = $this->getData();

            if ($curData) {
                foreach ($data['rows'] as $name => $row) {
                    $curRow = $curData['rows'][$name] ?? (!empty($row['history'])
                        ? $this->getHistoryRow($row['history'], $curData)
                        : null
                    );

                    if ($curRow && $curRow['value'] != $curRow['default_value']) {
                        $data['rows'][$name]['value'] = $curRow['value'];
                    }
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Get
     * 
     * @param array $history
     * @param array $curData
     * 
     * @return ?array
     */
    protected function getHistoryRow(array $history, array &$curData): ?array
    {
        foreach ($history as $name) {
            if (isset($curData['rows'][$name])) {
                return $curData['rows'][$name];
            }
        }

        return null;
    }
}
