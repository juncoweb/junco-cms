<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Mysql;

use Junco\Database\Schema\Interface\TriggersInterface;
use Junco\Database\Schema\Interface\Entity\TriggerInterface;
use Junco\Database\Schema\Mysql\Entity\Trigger;
use Database;

class Triggers implements TriggersInterface
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
     * @param string $Name
     * 
     * @return bool
     */
    public function has(string $Name): bool
    {
        return (bool)$this->db->query("SHOW TRIGGERS WHERE Trigger = ?", $Name)->fetchColumn();
    }

    /**
     * Fetch all
     * 
     * @param array $where
     * 
     * @return TriggerInterface[]
     */
    public function fetchAll(array $where = []): array
    {
        if ($where) {
            foreach ($where as $column => $value) {
                if ($column == 'Name') {
                    $column = 'Trigger';
                }

                if (is_string($value)) {
                    $this->db->where("`$column` = ?", $value);
                } else {
                    $this->db->where("`$column` IN ( ?.. )", $value);
                }
            }
        }

        $triggers = $this->db->query("SHOW TRIGGERS [WHERE]")->fetchAll();

        return array_map(fn($trigger) => new Trigger(
            $trigger['Trigger'],
            $trigger['Table'],
            $trigger['Timing'],
            $trigger['Event'],
            $trigger['Statement']
        ), $triggers);
    }

    /**
     * Fetch
     * 
     * @param string $Name
     * 
     * @return ?TriggerInterface
     */
    public function fetch(string $Name): ?TriggerInterface
    {
        return $this->fetchAll(['Name' => $Name])[0] ?? null;
    }

    /**
     * Create
     * 
     * @param TriggerInterface $trigger
     * 
     * @return int
     */
    public function create(TriggerInterface $trigger): int
    {
        $query = $trigger->getCreateStatement();
        $Name  = $trigger->getName();

        $this->db->exec("DROP TRIGGER IF EXISTS `$Name`"); // there should be a rollback !!!!!

        return $this->db->exec($query);
    }

    /**
     * Drop
     * 
     * @param string|array $Name
     * 
     * @return int
     */
    public function drop(string|array $Name): int
    {
        if (is_array($Name)) {
            $Name = implode('`, `', $Name);
        }

        return $this->db->exec("DROP TRIGGER IF EXISTS `$Name`");
    }

    /**
     * New
     * 
     * @param string $Name
     * 
     * @return TriggerInterface
     */
    public function newTrigger(string $Name): TriggerInterface
    {
        return new Trigger($Name);
    }

    /**
     * From
     * 
     * @param array $Data
     * 
     * @return ?TriggerInterface
     */
    public function from(array $Data): ?TriggerInterface
    {
        return Trigger::from($Data);
    }
}
