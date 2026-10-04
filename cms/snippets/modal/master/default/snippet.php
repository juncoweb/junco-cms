<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Modal\ModalFormInterface;
use Junco\Modal\ModalInterface;
use Junco\Responder\ResponderBase;
use Psr\Http\Message\ResponseInterface;

class modal_master_default_snippet extends ResponderBase implements ModalInterface
{
    // vars
    protected array  $json    = [];
    protected array  $buttons = [];
    protected array  $hidden  = [];
    protected string $content = '';
    protected string $enter   = '';
    //
    protected ?ModalFormInterface $form = null;

    /**
     * Type
     * 
     * @param string $type
     * @param array  $attr
     * 
     * @return void
     */
    public function type(string $type, array $attr = []): void
    {
        switch ($type) {
            case 'delete':
                $title = $this->enter = _t('Delete');
                $icon = 'fa-solid fa-trash';

                $this->json['type'] = 'alert';
                break;

            case 'trash':
                $title = $this->enter = _t('Trash');
                $icon = 'fa-solid fa-trash';

                $this->json['type'] = 'alert';
                break;

            case 'create':
                $title = _t('Create');
                $icon = 'fa-solid fa-plus';
                break;

            case 'edit':
                $title = _t('Edit');
                $icon = 'fa-solid fa-pencil';
                break;

            case 'status':
                $icon  = 'fa-solid fa-circle';
                $title = $attr['title'];

                $this->json['color'] = $attr['color'];
                $this->enter = _t('Change');
                break;

            case 'alert':
                $this->json['type'] = 'alert';
                return;
        }
        $this->json['title'] = $title;
        $this->json['icon'] = $icon;
    }

    /**
     * Size
     */
    public function size(string $size): void
    {
        $this->json['size'] = $size;
    }

    /**
     * Button
     * 
     * @param string $control
     * @param string $title
     * @param string $caption
     * 
     * @return void
     */
    public function button(string $control = '', string $title = '', string $caption = ''): void
    {
        if (!$title) {
            $title = _t('Button');
        }

        $this->buttons[] = [
            'type'    => 'button',
            'control' => $control,
            'title'   => $title,
            'caption' => $caption ?: $title
        ];
    }

    /**
     * Enter
     * 
     * @param string $title
     * @param string $caption
     * 
     * @return void
     */
    public function enter(string $title = '', string $caption = ''): void
    {
        if (!$title) {
            $title = $this->enter ?: _t('Enter');
        }

        $this->buttons[] = [
            'type'    => 'submit',
            'title'   => $title,
            'caption' => $caption ?: $title
        ];
    }

    /**
     * Close
     * 
     * @param string $title
     * @param string $caption
     * 
     * @return void
     */
    public function close(string $title = '', string $caption = ''): void
    {
        if (!$title) {
            $title = _t('Close');
        }

        $this->buttons[] = [
            'type'    => 'close',
            'title'   => $title,
            'caption' => $caption ?: $title
        ];
    }

    /**
     * Form
     * 
     * @param string $id
     * 
     * @return ModalFormInterface
     */
    public function getForm(string $id = ''): ModalFormInterface
    {
        return $this->form = snippet('modal#form', '', $id);
    }

    /**
     * Set the pathway of the page
     *
     * @param string|array $value
     * 
     * @return void
     */
    public function pathway(string|array $value): void
    {
        $this->json['pathway'] = $value;
    }

    /**
     * Set the title
     *
     * @param string|array $title
     * @param string       $icon
     * @param string       $color
     * 
     * @return void
     */
    public function title($title, string $icon = '', string $color = ''): void
    {
        $this->json['title'] = is_array($title)
            ? implode(' &gt; ', $title)
            : $title;

        if ($icon) {
            $this->json['icon'] = $icon;
        }
        if ($color) {
            $this->json['color'] = $color;
        }
    }

    /**
     * Help link
     *
     * @param string $url
     * 
     * @return void
     */
    public function helpLink(string $url): void
    {
        $this->json['help_url'] = $url;
        $this->json['help_title'] = _t('Help');
    }

    /**
     * Content
     *
     * @param string $html
     * 
     * @return void
     */
    public function content(string $html): void
    {
        $this->content = $html;
    }

    /**
     * Footer
     *
     * @param string $html
     * 
     * @return void
     */
    public function footer(string $html = ''): void
    {
        $this->json['footer_html'] = $html;
    }

    /**
     * Creates a simplified response with a message.
     * 
     * @param string $message
     * @param int    $statusCode
     * @param int    $code
     * 
     * @return ResponseInterface
     */
    public function responseWithMessage(string $message = '', int $statusCode = 0, int $code = 0): ResponseInterface
    {
        if ($code) {
            $message = sprintf('%d - %s', $code, $message);
        }

        if (strlen($message) > 240) {
            $this->size('large');
        }

        $this->close(_t('Close'));
        $this->title(_t('Alert'));
        $this->content = $message;

        return $this->response($statusCode);
    }

    /**
     * Create a response.
     * 
     * @param int $statusCode
     * @param string $reasonPhrase
     * 
     * @return ResponseInterface
     */
    public function response(int $statusCode = 200, string $reasonPhrase = ''): ResponseInterface
    {
        // profiler
        if (config('system.profiler')) {
            $this->json['__profiler'] = app('profiler')->render(true);
        }

        // content
        $this->json['content'] = ob_get_contents() . $this->content;
        ob_end_clean();

        // buttons
        if ($this->buttons) {
            $this->json['buttons'] = $this->buttons;
        }

        // form
        if ($this->form !== null) {
            $this->form->merge($this->json);
        }

        return $this->createJsonResponse($this->json, $statusCode, $reasonPhrase);
    }
}
