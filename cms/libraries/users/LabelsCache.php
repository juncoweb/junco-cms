<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Users;

use Junco\System\Etc;
use LanguageHelper;

class LabelsCache
{
    /**
     * 
     */
    public function update(): void
    {
        $basename = 'users-labels';
        $filename = $basename . '.php';
        $buffer   = $this->getBuffer($translate);

        //
        (new Etc)->store($filename, $buffer);

        if ($translate) {
            (new LanguageHelper())->translate($basename, $translate);
        }
    }

    /**
     * Get
     */
    protected function getBuffer(?array &$translate = null): string
    {
        $rows = db()->query("
		SELECT
		 l.id ,
		 l.extension_id ,
		 l.label_key ,
		 l.label_description ,
		 e.extension_alias
		FROM `#__users_roles_labels` l
		LEFT JOIN `#__extensions` e ON ( l.extension_id = e.id )
		ORDER BY e.extension_alias, l.label_key")->fetchAll();

        $EOL       = PHP_EOL;
        $translate = [];
        $buffer    = '';

        foreach ($rows as $row) {
            if ($row['label_description']) {
                $translate[] = $row['label_description'];
            }

            if ($row['label_key']) {
                $row['label_key'] = '_' . $row['label_key'];
            }

            $name = 'L_' . strtoupper($row['extension_alias'] . $row['label_key']);
            $value = $row['id'];
            $buffer .= "define('$name', $value);$EOL";
        }

        return "<?php{$EOL}{$EOL}/**{$EOL} * Labels{$EOL} */{$EOL}$buffer{$EOL}?>";
    }
}
