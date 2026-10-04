<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Modal\ModalFormInterface;

class modal_master_default_form implements ModalFormInterface
{
    // vars
    protected string $content = '';
    protected array  $form    = [];

    /**
     * Constructor
     * 
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->form['id'] = ($id ?: 'js-form');
        $this->form['hidden'][] = FormSecurity::getToken(true);
    }

    /**
     * Hidden
     * 
     * @param string $name
     * @param mixed  $value
     * 
     * @return self
     */
    public function hidden(string $name, mixed $value): self
    {
        if (is_array($value)) {
            foreach ($value as $key => $value) {
                $this->form['hidden'][] = [
                    'name' => $name . '[' . $key . ']',
                    'value' => $value
                ];
            }
        } else {
            $this->form['hidden'][] = [
                'name' => $name,
                'value' => $value
            ];
        }

        return $this;
    }

    /**
     * Question
     * 
     * @param int|array       $total
     * @param callable|string $callback
     * 
     * @return self
     */
    public function question(int|array $total = 1, callable|string $callback = ''): self
    {
        $total = is_array($total)
            ? count($total)
            : (int)$total;

        if (is_string($callback)) {
            $question = match ($callback) {
                'status' => _nt('Are you sure to change the status of the selected item?', 'Are you sure to change the status of the %d selected item?', $total),
                'trash' => _nt('Are you sure you want to trash the selected item?', 'Are you sure you want to trash the %d selected items?', $total),
                default => _nt('Are you sure you want to delete the selected item?', 'Are you sure you want to delete the %d selected items?', $total)
            };
        } else {
            $question = call_user_func($callback, $total);
        }

        $this->content = '<p>' . sprintf($question, $total) . '</p>';

        return $this;
    }

    /**
     * Merge
     * 
     * @param array &$json
     * 
     * @return void
     */
    public function merge(array &$json): void
    {
        $json['content'] .= $this->content;
        $json['form'] = $this->form;
    }
}
