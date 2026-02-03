<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database;

class Prefixer
{
    // vars
    protected string $universal_prefix = '#__';
    protected array  $replaces         = [];

    /**
     * Constructor
     * 
     * @param string $prefix
     */
    public function __construct(
        protected string $prefix = ''
    ) {}

    /**
     * Set
     * 
     * @param array $tbl_names
     * 
     * @return self
     */
    public function setTables(array $tbl_names): self
    {
        foreach ($tbl_names as $tbl_name) {
            $this->replaces['`' . $tbl_name . '`'] = '`' . $this->putUniversalOnTableName($tbl_name) . '`';
        }

        return $this;
    }

    /**
     * Replace with local prefix
     * 
     * @param string  $query   The sql query string
     * 
     * @return string
     */
    public function replaceWithLocal(string $query): string
    {
        return str_replace($this->universal_prefix, $this->prefix, $query);
    }

    /**
     * Replace with universal prefix
     *
     * @param string        $query      The sql query string
     * @param string|array  $tbl_names  The tables to replace
     * 
     * @return string The string with replacements
     */
    public function replaceWithUniversal(string $query): string
    {
        return strtr($query, $this->replaces);
    }

    /**
     * Put
     *
     * @param string $tbl_name
     * 
     * @return string The string with replacements
     */
    public function putUniversalOnTableName(string $tbl_name): string
    {
        if ($this->prefix) {
            $tbl_name = preg_replace('%^' . $this->prefix . '%', '', $tbl_name);
        }

        return $this->universal_prefix . $tbl_name;
    }

    /**
     * Remove
     *
     * @param string $tbl_name  The sql table name
     * 
     * @return string The string with replacements
     */
    public function removeAllOnTableName(string $tbl_name): string
    {
        $regex = ['/^#__/'];
        if ($this->prefix) {
            $regex[] = '%^' . $this->prefix . '%';
        }

        return preg_replace($regex, '', $tbl_name);
    }

    /**
     * Force
     *
     * @param string $tbl_name  The sql table name
     */
    public function forceLocalOnTableName(string $tbl_name): string
    {
        return $this->prefix . $this->removeAllOnTableName($tbl_name);
    }
}
