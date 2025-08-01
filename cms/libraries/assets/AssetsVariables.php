<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class AssetsVariables
{
    //
    protected string  $basepath = SYSTEM_ABSPATH;
    protected ?string $file     = null;

    /**
     * Is
     * 
     * @param string $theme_key
     * 
     * @return bool
     */
    public function isTheme(string $theme_key): bool
    {
        return (bool)$this->getFileOfThemeVariables($theme_key);
    }

    /**
     * Get
     * 
     * @param string $search
     * 
     * @return array
     */
    public function getSources(string $search): array
    {
        $rows = [];
        $quests = [
            ['pattern' => 'app/*/sass/partials/_variables.scss', 'scope' => 'App'],
            ['pattern' => 'cms/scripts/*/sass/partials/_variables.scss', 'scope' => 'Script'],
            ['pattern' => 'cms/plugins/*/*/*/sass/partials/_variables.scss', 'scope' => 'Plugin'],
        ];

        foreach ($quests as $quest) {
            $files = glob($this->basepath . $quest['pattern']) ?: [];

            foreach ($files as $file) {
                $file   = str_replace($this->basepath, '', $file);
                $name   = $this->getNameOfFile($file, $quest['scope']);
                $rows[] = [
                    'file'  => $file,
                    'name'  => $name,
                    'scope' => $quest['scope']
                ];
            }
        }

        if ($search) {
            $this->searchFilter($rows, $search);
        }

        return $rows;
    }

    /**
     * Get
     */
    public function getData(string $file, string $theme_key = ''): array
    {
        $variables = $this->readVariables($file);

        return $this->readThemeVariables($theme_key, $variables);
    }

    /**
     * Update
     */
    public function update(string $theme_key, string $file, array $variables): bool
    {
        $varFile = $this->getFileOfThemeVariables($theme_key);

        if (!$varFile) {
            return false;
        }

        $partials = $this->explodeThemeVariables($varFile);
        $partial  = isset($partials[$file])
            ? $this->getCurrentVariables($partials[$file])
            : [];

        // update
        foreach ($variables as $var) {
            if ($var['value'] && $var['value'] != $var['default']) {
                $partial[$var['name']] = $var['value'];
            } elseif (isset($partial[$var['name']])) {
                unset($partial[$var['name']]);
            }
        }

        // save partial
        if ($partial) {
            $partials[$file] = $file . PHP_EOL . $this->renderVariables($partial);
        } elseif (isset($partials[$file])) {
            unset($partials[$file]);
        }

        // write
        return file_put_contents($varFile, $this->renderPartials($partials)) !== false;
    }

    /**
     * Get
     * 
     * @param string $key
     * 
     * @return string
     */
    protected function getFileOfThemeVariables(string $key): string
    {
        if ($this->file === null) {
            $file = (new AssetsThemes)->getScssVarFile($key);

            $this->file = is_readable($file)
                ? $file
                : '';
        }

        return $this->file;
    }

    /**
     * Get
     * 
     * @param string $file
     * 
     * @return ?string
     */
    protected function getNameOfFile(string $file, string $scope): ?string
    {
        switch ($scope) {
            case 'App':
                preg_match('#app\/(.*)?\/sass\/partials\/_variables\.scss#', $file, $match);
                return $match[1];

            case 'Script':
                preg_match('#cms\/scripts\/(.*)?\/sass\/partials\/_variables\.scss#', $file, $match);
                return $match[1];

            case 'Plugin':
                preg_match('#cms\/plugins\/(.*)?\/(.*)?\/(.*)?\/sass\/partials\/_variables\.scss#', $file, $match);
                return $match[1] . '.' . $match[2] . '.' . $match[3];
        }

        return null;
    }

    /**
     * Get
     */
    protected function searchFilter(array &$rows, string $search): void
    {
        $pattern = '/' . preg_quote($search, '/') . '/i';

        $rows = array_filter($rows, function ($row) use ($pattern) {
            return preg_match($pattern, $row['name']);
        });
    }

    /**
     * Get
     */
    protected function explodeThemeVariables(string $varFile): array
    {
        $blocks = explode('// ', file_get_contents($varFile));
        $partials = [];

        foreach ($blocks as $partial) {
            $partial = trim($partial);
            $index   = preg_split('#(\r|\n)#', $partial, 2);
            $index   = trim($index[0]);

            if ($index) {
                $partials[$index] = $partial . PHP_EOL;
            }
        }

        return $partials;
    }

    /**
     * Get
     */
    protected function renderVariables(array $variables): string
    {
        $buffer = '';
        foreach ($variables as $name => $value) {
            $buffer .= '$' . $name . ': ' . $value . ' !default;' . PHP_EOL;
        }

        return $buffer;
    }

    /**
     * Get
     */
    protected function renderPartials(array $partials): string
    {
        ksort($partials);

        return $partials ? '// ' . implode('// ', $partials) : '';
    }

    /**
     * Get
     */
    protected function getCurrentVariables(string $partial): array
    {
        $variables = [];
        $pattern   = '#\$([\w-]+)\s*:\s*([^;]*?)(?:\!default)?;#';

        if (preg_match_all($pattern, $partial, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $variables[$match[1]] = htmlentities(trim($match[2]));
            }
        }

        return $variables;
    }

    /**
     * Read
     */
    protected function readVariables(string $file): array
    {
        $file = $this->basepath . $file;

        if (!$this->isFile($file)) {
            return [];
        }

        $variables = [];
        $contents  = file_get_contents($file);
        $pattern   = '#\$([\w-]+)\s*:\s*([^;]*?)(?:\!default)?;#';

        if (preg_match_all($pattern, $contents, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $variables[$match[1]] = [
                    'name'    => $match[1],
                    'value'   => false,
                    'default' => htmlentities(trim($match[2])),
                ];
            }
        }

        return $variables;
    }

    /**
     * Read
     */
    protected function readThemeVariables(string $theme_key, array $variables): array
    {
        $file = $this->getFileOfThemeVariables($theme_key);

        if ($file) {
            $contents = file_get_contents($file);
            $pattern  = '#\$([\w-]+)\s*:\s*(.*?)(?:\!default)?;#m';

            if (preg_match_all($pattern, $contents, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    if (isset($variables[$match[1]])) {
                        $variables[$match[1]]['value'] = htmlentities(trim($match[2]));
                    }
                }
            }
        }

        return $variables;
    }

    /**
     * Is
     */
    protected function isFile(string $file): bool
    {
        $info = pathinfo($file);

        return is_file($file)
            && isset($info['extension'])
            && $info['extension'] == 'scss'
            && substr($info['basename'], 0, 10) == '_variables';
    }
}
