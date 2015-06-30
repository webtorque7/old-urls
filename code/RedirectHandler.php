<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 30/06/15
 * Time: 2:37 PM
 */

class RedirectHandler extends Extension
{
    public function onBeforeHTTPError404($request) {
        $url = strtolower($request->getURL());
        $oldPage = OldURLRedirect::get_from_url($url);

        // If there's a match, direct!
        if($oldPage) {
            $response = new SS_HTTPResponse();
            $dest = $oldPage->getRedirectionLink();
            $response->redirect(Director::absoluteURL($dest), 301);
            throw new SS_HTTPResponse_Exception($response);
        }
    }
} 