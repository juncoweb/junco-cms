<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Extensions;

class Extensions
{
    // vars
    static $alias  = '';
    static $tables = null;

    /**
     * Validate the alias.
     */
    public static function validate(string &$alias)
    {
        $alias = strtolower($alias);

        return preg_match('/^[a-z][a-z0-9]{1,23}$/', $alias);
    }

    /**
     * Returns a list with all system components.
     * 
     * @deprecated
     */
    public static function getComponents(): array
    {
        return [
            'a'    => ['name' => 'Application', 'source' => 'app/%s/',            'local' => 'app/%s/',                    'clean' => true],
            'm'    => ['name' => 'Media',        'source' => 'media/%s/',        'local' => SYSTEM_MEDIA_PATH . '%s/',    'clean' => false],
            'j'    => ['name' => 'Scripts',    'source' => 'cms/scripts/%s/',    'local' => 'cms/scripts/%s/',            'clean' => true],
            'k'    => ['name' => 'Snippets',    'source' => 'cms/snippets/%s/',    'local' => 'cms/snippets/%s/',            'clean' => true],
            'l'    => ['name' => 'Libraries',    'source' => 'cms/libraries/%s/', 'local' => 'cms/libraries/%s/',        'clean' => true],
            'p'    => ['name' => 'Plugins',    'source' => 'cms/plugins/%s/',    'local' => 'cms/plugins/%s/',            'clean' => true],
            'v'    => ['name' => 'Vendor',        'source' => 'vendor/%s/',        'local' => 'vendor/%s/',                'clean' => true],
        ];
    }

    /**
     * Returns a list with all sql queries.
     */
    public static function getQueries(string $alias, bool $as_array = false): array
    {
        $db      = db();
        $schema  = $db->getSchema();
        $queries = [];
        $regex   = '/^' . $db->getPrefix() . '(' . $alias . '(?:_.*)?)$/';

        // query - tables
        self::$tables ??= $schema->tables()->list();

        foreach (self::$tables as $tbl_name) {
            if (preg_match($regex, $tbl_name, $match)) {
                $queries[] = [
                    'Type' => 'TABLE',
                    'Name' => $match[1]
                ];
            }
        }

        // triggers
        if ($queries) {
            $rows = $schema->triggers()->fetchAll(['Table' => array_column($queries, 'Name')]);

            foreach ($rows as $row) {
                $queries[] = [
                    'Type' => 'TRIGGER',
                    'Name' => $row['Trigger']
                ];
            }
        }
        $alias_2 = ucfirst($alias);

        // query - routines
        $regex = "^[a-z]*{$alias_2}.*";
        foreach ($schema->routines()->fetchAll(['Search' => $alias]) as $row) {
            $queries[] = [
                'Type' => $row['Type'],
                'Name' => $row['Name']
            ];
        }

        //
        if (!$as_array) {
            foreach ($queries as $i => $row) {
                $queries[$i] = ($row['Type'] == 'TABLE' ? '' : $row['Type'] . ':') . $row['Name'];
            }
        }

        return $queries;
    }
}
