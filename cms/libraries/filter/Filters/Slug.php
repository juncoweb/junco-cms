<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filter\Filters;

class Slug extends FilterAbstract
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->type = 'string';
        $this->default = '';

        //
        $this->callback[] = [$this, 'sanitize'];
    }

    /**
     * Transform a text to serve as a slug
     * 
     * @param string $slug
     * 
     * @return string
     */
    protected function sanitize(string &$slug, string $replace = '-'): void
    {
        $slug = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'à', 'è', 'ì', 'ò', 'ù', 'â', 'ê', 'î', 'ô', 'û', 'ä', 'ë', 'ï', 'ö', 'ü', 'Á', 'É', 'Í', 'Ó', 'Ú', 'À', 'È', 'Ì', 'Ò', 'Ù', 'Â', 'Ê', 'Î', 'Ô', 'Û', 'Ä', 'Ë', 'Ï', 'Ö', 'Ü', 'ñ', 'Ñ', 'ç', 'Ç', 'º', 'ª'],
            ['a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'n', 'n', 'c', 'c', 'o', 'a'],
            trim($slug)
        );

        $slug = strtolower(preg_replace('%[^\w-]%', $replace, $slug));
    }
}
