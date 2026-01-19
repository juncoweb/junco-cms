<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

abstract class MenusBase implements MenusInterface
{
    // vars
    protected Menus  $menus;
    protected string $HR               = ';';
    protected string $EOL              = '';
    protected string $TAB              = '';
    protected bool   $only_if_has_edge = false;

    /**
     * Constructor
     * 
     * @param string $key
     */
    public function __construct(string $key = '')
    {
        $this->menus = new Menus($key);

        /*if (false) {
			$this->EOL	= PHP_EOL;
			$this->TAB	= "\t";
		}*/
    }

    /**
     * Set to show only rows with edges.
     * 
     * @param bool $value
     * 
     * @return self
     */
    public function setWithEdges(bool $value = true): self
    {
        $this->only_if_has_edge = $value;
        return $this;
    }

    /**
     * Render
     * 
     * @return string
     */
    public function render(): string
    {
        return $this->build($this->menus->read());
    }

    /**
     * Build
     * 
     * @param array $rows
     * @param int   $i
     * 
     * @return string
     */
    protected abstract function build(array $rows, int $i = 0): string;
}
