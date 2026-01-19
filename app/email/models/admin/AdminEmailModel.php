<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class AdminEmailModel extends Model
{
    /**
     * Send
     */
    public function getMessageData()
    {
        $data = $this->filter(GET, ['layout' => 'text|default:default']);

        //
        $url     = url('admin/email/message', ['layout' => '%s']);
        $layouts = SystemHelper::scanSnippets('email');
        $data += [
            'message' => null
        ];

        foreach ($layouts as $name => $caption) {
            $layouts[$name] = ['url' => sprintf($url, $name), 'caption' => $caption];
        }

        if ($data['layout']) {
            if (isset($layouts[$data['layout']])) {
                // message
                $message = Email::getMessage($data['layout']);
                $message->legend('This is a <a href="www.example.com">legend</a>');
                $message->line('Lorem ipsum dolor <a href="www.example.com">sit amet</a>, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.');
                $message->codelink('#codelink');
                $message->legal();
                $data['message'] = [$message->getHtml(), $message->getPlain()];
            } else {
                $data['layout'] = '';
            }
        }

        return $data + ['layouts' => $layouts];
    }

    /**
     * Debug
     */
    public function getDebugData()
    {
        //$this->filter(GET, ['layout' => 'text|default:default']);

        return [
            'values' => [
                'transport' => 'system',
                'subject' => 'This is a test',
                'to' => config('site.email'),
                'message_plain' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
                'message_html' => '<h1>Lorem ipsum</h1>'
                    . '<p><b>Lorem ipsum</b> dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>'
                    . '<p><i>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</i></p>'
                    . '<h2>Duis aute</h2>'
                    . '<p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>'
                    . '<h2>Excepteur</h2>'
                    . '<p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>'
            ]
        ];
    }
}
