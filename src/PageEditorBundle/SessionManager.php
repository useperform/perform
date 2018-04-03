<?php

namespace Perform\PageEditorBundle;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Starts, stops, and detects a page editing session.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SessionManager
{
    const SESSION_KEY = 'perform_page_editor.session';

    protected $excludedUrlRegexes = [];

    public function __construct(array $excludedUrlRegexes = [])
    {
        $this->excludedUrlRegexes = $excludedUrlRegexes;
    }

    public function start(SessionInterface $session)
    {
        // dispatch an event
        // set the time the session started
        $session->set(self::SESSION_KEY, true);
    }

    public function stop(SessionInterface $session)
    {
        // dispatch an event
        $session->remove(self::SESSION_KEY);
    }

    public function isEditing(SessionInterface $session = null)
    {
        return $session && $session->get(self::SESSION_KEY) === true;
    }

    public function requestIsEditing(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            return false;
        }
        if (!$this->isEditing($request->getSession())) {
            return false;
        }

        $url = $request->getPathinfo();
        foreach ($this->excludedUrlRegexes as $regex) {
            if (preg_match('`'.$regex.'`', $url)) {
                return false;
            }
        }

        return true;
    }
}
