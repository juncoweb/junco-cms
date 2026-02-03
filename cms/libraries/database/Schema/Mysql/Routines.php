<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Mysql;

use Junco\Database\Schema\Interface\RoutinesInterface;
use Junco\Database\Schema\Interface\Entity\RoutineInterface;
use Junco\Database\Schema\Mysql\Entity\Routine;
use Database;

class Routines implements RoutinesInterface
{
    //
    protected $db;

    /**
     * Constructor
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Has
     * 
     * @param string $Type
     * @param string $Name
     * 
     * @return bool
     */
    public function has(string $Type, string $Name): bool
    {
        return (bool)$this->db->query("
		SELECT COUNT(*)
		FROM `information_schema`.`ROUTINES`
        WHERE `ROUTINE_TYPE` = ?
		AND `ROUTINE_NAME` = ?", $Type, $Name)->fetchColumn();
    }

    /**
     * Fetch
     * 
     * @param array $where
     * @param bool  $optimize   It does not add the parameters to improve performance.
     * 
     * @return RoutineInterface[]
     */
    public function fetchAll(array $where = [], bool $optimize = false): array
    {
        // query
        $this->db->where("ROUTINE_SCHEMA = DATABASE()");

        if ($where) {
            foreach ($where as $column => $value) {
                if ($column === 'Search') {
                    $this->db->where("`ROUTINE_NAME` REGEXP ?", "^[a-z]*" . ucfirst($value) . ".*");
                } else {
                    $column = $this->getColumnName($column) or abort();

                    if (is_string($value)) {
                        $this->db->where("`$column` = ?", $value);
                    } else {
                        $this->db->where("`$column` IN ( ?.. )", $value);
                    }
                }
            }
        }

        $routines = $this->db->query("
		SELECT
		 ROUTINE_SCHEMA,
		 ROUTINE_NAME,
		 ROUTINE_TYPE,
		 DEFINER,
         ROUTINE_DEFINITION,
		 CREATED,
		 LAST_ALTERED,
		 ROUTINE_COMMENT
		FROM `information_schema`.`ROUTINES`
		[WHERE]
		ORDER BY ROUTINE_NAME")->fetchAll();

        if ($optimize) {
            return array_map(
                fn($routine) => new Routine(
                    $routine['ROUTINE_TYPE'],
                    $routine['ROUTINE_NAME'],
                    $routine['ROUTINE_DEFINITION'],
                    $routine['ROUTINE_COMMENT']
                ),
                $routines
            );
        }

        $this->setRoutineExtra($routines);

        return array_map(
            fn($routine) => new Routine(
                $routine['ROUTINE_TYPE'],
                $routine['ROUTINE_NAME'],
                $routine['ROUTINE_DEFINITION'],
                $routine['ROUTINE_COMMENT'],
                $routine['PARAMETERS'],
                $routine['RETURNS'],
            ),
            $routines
        );
    }

    /**
     * Fetch
     * 
     * @param string $Name
     * 
     * @return ?RoutineInterface
     */
    public function fetch(string $Name): ?RoutineInterface
    {
        $routines = $this->fetchAll(['Name' => $Name]);

        return $routines[0] ?? null;
    }

    /**
     * Create
     * 
     * @param RoutineInterface $Routine
     * 
     * @return int
     */
    public function create(RoutineInterface $Routine): int
    {
        $query = $Routine->getCreateStatement();
        $Type  = $Routine->getType();
        $Name  = $Routine->getName();

        $this->db->exec("DROP $Type IF EXISTS `$Name`"); // there should be a rollback !!!!!

        return $this->db->exec($query);
    }

    /**
     * Drop
     * 
     * @param string $Type
     * @param string $Name
     * 
     * @return int
     */
    public function drop(string $Type, string $Name): int
    {
        return $this->db->exec("DROP $Type IF EXISTS `$Name`");
    }

    /**
     * New
     * 
     * @param string $Type
     * @param string $Name
     * 
     * @return RoutineInterface
     */
    public function newRoutine(string $Type, string $Name): RoutineInterface
    {
        return new Routine($Type, $Name);
    }

    /**
     * From
     * 
     * @param array $Data
     * 
     * @return ?RoutineInterface
     */
    public function from(array $Data): ?RoutineInterface
    {
        return Routine::from($Data);
    }

    /**
     * Set
     */
    protected function setRoutineExtra(array &$routines): void
    {
        foreach ($routines as &$routine) {
            $routine['QUERY'] = $this->removeDefiner(
                $this->db->query("SHOW CREATE $routine[ROUTINE_TYPE] `$routine[ROUTINE_NAME]`")->fetchColumn(2)
            );

            if ($routine['ROUTINE_TYPE'] == 'PROCEDURE') {
                $this->setProcedureData($routine);
            } else {
                $this->getFunctionData($routine);
            }
        }
    }

    /**
     * Remove
     */
    protected function removeDefiner(string $query): string
    {
        return preg_replace('/^CREATE .*(PROCEDURE|FUNCTION)( IF NOT EXISTS)?/sU', 'CREATE $1', $query);
    }

    /**
     * Get
     */
    protected function setProcedureData(array &$routine): void
    {
        preg_match('/^CREATE\s+PROCEDURE\s+[`]?\w+[`]?\s*\((?<parameters>.*)\)(?<characteristic>[^)]*)?BEGIN/sU', $routine['QUERY'], $match);

        $routine['PARAMETERS'] = trim($match['parameters'] ?? '');
        $routine['RETURNS']    = '';
    }

    /**
     * Get
     */
    protected function getFunctionData(array &$routine): void
    {
        preg_match('/^CREATE\s+FUNCTION\s+[`]?\w+[`]?\s*\((?<parameters>.*)\)\s+RETURNS(?<returns>.*)BEGIN/sU', $routine['QUERY'], $match);

        $routine['PARAMETERS'] = trim($match['parameters'] ?? '');
        $routine['RETURNS']    = trim($match['returns'] ?? '');
    }

    /**
     * Get
     */
    protected function getColumnName(string $Column): ?string
    {
        return match ($Column) {
            'Name' => 'ROUTINE_NAME',
            'Type' => 'ROUTINE_TYPE',
            default => null
        };
    }
}
