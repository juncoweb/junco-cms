<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Mysql;

use Exception;

trait FieldsTrait
{
    /**
     * Get Field Statement
     * 
     * @param array	  $Field
     * 
     * @return string
     */
    protected function getFieldStatement(array $Field): string
    {
        $sql = $Field['Type'];

        if (!empty($Field['TypeLength'])) {
            $sql .= "($Field[TypeLength])";
        }
        if (!empty($Field['Attribute'])) {
            $sql .= " $Field[Attribute]";
        }
        if (!empty($Field['Collation'])) {
            $sql .= $this->getCollationStatement($Field['Collation']);
        }
        $sql .= $Field['Null'] == 'YES' ? " NULL" : " NOT NULL";

        if ($Field['Default'] !== null) {
            if (!in_array($Field['Default'], ['NULL', 'CURRENT_TIMESTAMP']) && !is_numeric($Field['Default'])) {
                $Field['Default'] = $this->db->quote($Field['Default']);
            }

            $sql .= " DEFAULT $Field[Default]";
        }
        if (!empty($Field['Extra'])) {
            $Field['Extra'] = str_replace('DEFAULT_GENERATED', '', $Field['Extra']);

            if ($Field['Extra']) {
                $sql .= " $Field[Extra]";
            }
        }
        if (!empty($Field['Comment'])) {
            $sql .= " COMMENT " . $this->db->quote($Field['Comment']);
        }
        if (!empty($Field['Position'])) {
            $sql .= ($Field['Position'] == 'FIRST')
                ? " $Field[Position]"
                : " AFTER `$Field[Position]`";
        }

        return $sql;
    }

    /**
     * Get
     * 
     * @param string  $Collation
     * 
     * @return string
     */
    protected function getCollationStatement(string $Collation): string
    {
        $Charset = explode('_', $Collation)[0];

        return " CHARACTER SET $Charset COLLATE $Collation";
    }
}
