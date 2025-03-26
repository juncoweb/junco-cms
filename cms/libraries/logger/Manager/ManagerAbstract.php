<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Logger\Manager;

use Junco\Logger\Manager\ManagerInterface;

abstract class ManagerAbstract implements ManagerInterface
{
    /**
     * Verify
     * 
     * @param array $rows
     * @param bool  $delete
     * 
     * @return array
     */
    public function verifyDuplicates(array $rows, bool $delete = true): array
    {
        $keys = [];

        foreach ($rows as $i => $row) {
            $key = $this->getInternalKey($row);

            if (!in_array($key, $keys)) {
                $keys[] = $key;
            } elseif ($delete) {
                unset($rows[$i]);
            } else {
                $rows[$i]['status'] = 2;
            }
        }

        return $rows;
    }

    /**
     * Get
     * 
     * @param array $row
     * 
     * @return string
     */
    protected function getInternalKey(array $row): string
    {
        if ($row['context']) {
            $context = json_decode($row['context'], true);

            if (isset($context['file']) && isset($context['line'])) {
                return $context['file'] . ':' . $context['line'];
            }
        }

        return $row['message'];
    }
}
