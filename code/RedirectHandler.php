<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 30/06/15
 * Time: 2:37 PM
 */

class RedirectHandler extends Extension
{

    /**
     * @param SS_HTTPRequest $request
     * @throws SS_HTTPResponse_Exception
     */
    public function onBeforeHTTPError404($request) {
        $url = strtolower($this->getUrl($request));
        $oldPage = OldURLRedirect::get_from_url($url);

        // If there's a match, direct!
        if($oldPage) {
            $response = new SS_HTTPResponse();
            $dest = $oldPage->getRedirectionLink();
            $response->redirect(Director::absoluteURL($dest), 301);
            throw new SS_HTTPResponse_Exception($response);
        }
    }

    /**
     * Extract url, checks $_SERVER first to try and get raw url
     *
     * @param SS_HTTPRequest $request
     * @return string
     */
    public function getUrl($request)
    {
        if (!empty($_SERVER['REQUEST_URI'])) {
            return $_SERVER['REQUEST_URI'];
        }
        else if (!empty($_GET['url'])) {
            return $_GET['url'];
        }
        else {
            return $request->getURL();
        }
    }
} 