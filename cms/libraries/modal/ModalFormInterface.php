<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Modal;

interface ModalFormInterface
{
    /**
     * Hidden
     * 
     * @param string $name
     * @param mixed  $value
     * 
     * @return self
     */
    public function hidden(string $name, mixed $value): self;

    /**
     * Question
     * 
     * @param int|array $total
     * 
     * @return self
     */
    public function question(int|array $total = 1, ?callable $callback = null): self;

    /**
     * Merge
     * 
     * @param array &$json
     * 
     * @return void
     */
    public function merge(array &$json): void;
}
