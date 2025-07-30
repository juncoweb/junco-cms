<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Modal\ModalInterface;
use Junco\Mvc\Result;
use Junco\Responder\ResponderBase;
use Psr\Http\Message\ResponseInterface;

class modal_master_default_snippet extends ResponderBase implements ModalInterface
{
    // vars
    protected array $json    = [];
    protected array $buttons = [];
    protected array $hidden  = [];
    //
    public ?modal_form $form = null;
    public string $content   = '';

    /**
     * Type
     */
    public function type(string $type): void
    {
        $this->json['type'] = $type;
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
     */
    public function enter(string $title = '', string $caption = ''): void
    {
        if (!$title) {
            $title = _t('Enter');
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
     */
    public function form(string $id = ''): modal_form
    {
        return $this->form = new modal_form($id);
    }

    /**
     * Set the title
     *
     * @param string|array $title
     * @param string       $icon
     */
    public function title($title, string $icon = ''): void
    {
        $this->json['title'] = is_array($title)
            ? implode(' &gt; ', $title)
            : $title;

        if ($icon) {
            $this->json['icon'] = $icon;
        }
    }

    /**
     * Help link
     *
     * @param string $url
     */
    public function helpLink(string $url): void
    {
        $this->json['help_url'] = $url;
        $this->json['help_title'] = _t('Help');
    }

    /**
     * Footer
     *
     * @param string $html
     */
    public function footer(string $html = ''): void
    {
        $this->json['footer_html'] = $html;
    }

    /**
     * Creates a simplified response with a message.
     * 
     * @param Result|string $message
     * @param int $statusCode
     * @param int $code
     * 
     * @return ResponseInterface
     */
    public function responseWithMessage(Result|string $message = '', int $statusCode = 0, int $code = 0): ResponseInterface
    {
        if ($message instanceof Result) {
            $statusCode = $message->getStatusCode();
            $code       = $message->getCode();
            $message    = $message->getMessage();
        }

        if ($code) {
            $message = sprintf('%d - %s', $code, $message);
        }
        if (strlen($message) > 120) {
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
        if (isset($this->form)) {
            $this->json['form'] = $this->form->json;
        }

        return $this->createJsonResponse($this->json, $statusCode, $reasonPhrase);
    }
}

/**
 * Modal Form
 */
class modal_form
{
    public $json = [];

    /**
     * Constructor
     * 
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->json['id'] = ($id ?: 'js-form');
        $this->json['hidden'][] = FormSecurity::getToken(true);
    }

    /**
     * Hidden
     * 
     * @param string       $name
     * @param string|array $value
     */
    public function hidden(string $name = '', $value = '')
    {
        if (is_array($value)) {
            foreach ($value as $key => $value) {
                $this->json['hidden'][] = ['name' => $name . '[' . $key . ']', 'value' => $value];
            }
        } else {
            $this->json['hidden'][] = ['name' => $name, 'value' => $value];
        }
    }

    /**
     * Question
     * 
     * @param int|array $count
     */
    public function question($count = 1)
    {
        $count = is_array($count) ? count($count) : (int)$count;

        echo '<p>'
            . sprintf(_nt('Are you sure you want to delete the selected item?', 'Are you sure you want to delete the %d selected items?', $count), $count)
            . '</p>';
    }
}
