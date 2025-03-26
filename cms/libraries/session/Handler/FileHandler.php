<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Session\Handler;

class FileHandler implements \SessionHandlerInterface
{
    // vars
    protected $save_path = '';

    /**
     * Initialize session
     *
     * @param    string $save_path		The path where to store/retrieve the session.
     * @param    string $session_name	The session name.
     *
     * @return	 bool					The return value (usually true on success, false on failure).
     */
    public function open(string $save_path, string $session_name): bool
    {
        $this->save_path = $save_path . '/';
        return true;
    }

    /**
     * Close the session
     *
     * @return	 bool	The return value (usually true on success, false on failure).
     */
    public function close(): bool
    {
        return true;
    }

    /**
     * Read session data
     *
     * @param    string  $session_id	The session id.
     *
     * @return	 string					Returns an encoded string of the read data. If nothing was read, it must return false.
     */
    public function read(string $session_id): string|false
    {
        $file = $this->save_path . 'sess_' . $session_id;

        if (is_file($file)) {
            return file_get_contents($file);
        }
        return '';
    }

    /**
     * Write session data
     *
     * @param    string  $session_id	The session id.
     * @param    string  $data			The encoded session data.
     *
     * @return	 bool					The return value (usually true on success, false on failure).
     */
    public function write(string $session_id, string $data): bool
    {
        return file_put_contents($this->save_path . 'sess_' . $session_id, $data);
    }

    /**
     * Destroy a session
     *
     * @param    string  $session_id	The session ID being destroyed.
     *
     * @return	 bool					The return value (usually true on success, false on failure).
     */
    public function destroy(string $session_id): bool
    {
        $file = $this->save_path . 'sess_' . $session_id;

        if (is_file($file)) {
            return unlink($file);
        }

        return true;
    }

    /**
     * Cleanup old sessions
     *
     * @param    $max_lifetime	Sessions that have not updated for the last max_lifetime seconds will be removed.
     * 
     * @return	 int|false		Returns the number of deleted sessions on success, or false on failure.
     */
    public function gc(int $maxlifetime): int|false
    {
        $now = time();
        $total = 0;

        foreach (glob($this->save_path . 'sess_*') as $file) {
            if (is_file($file) && (filemtime($file) + $maxlifetime) < $now) {
                unlink($file);
                $total++;
            }
        }

        return $total;
    }
}
