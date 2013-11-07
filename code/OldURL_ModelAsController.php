<?php
/**
 * Need to overwrite the httpError function
 */

class OldURL_ModelAsController extends ModelAsController
{

        /**
         * Don't pass errorCode if response is a redirect
         *
         * @param int $errorCode
         * @param string $errorMessage Plaintext error message
         * @uses SS_HTTPResponse_Exception
         */
        public function httpError($errorCode, $errorMessage = null) {
                // Call a handler method such as onBeforeHTTPError404
                $this->extend('onBeforeHTTPError' . $errorCode, $this->request);

                // Call a handler method such as onBeforeHTTPError, passing 404 as the first arg
                $this->extend('onBeforeHTTPError', $errorCode, $this->request);

                if ($errorMessage instanceof SS_HTTPResponse && $errorMessage->getHeader('Location')) {
                        throw new SS_HTTPResponse_Exception($errorMessage, $errorMessage->getStatusCode());
                }
                // Throw a new exception
                throw new SS_HTTPResponse_Exception($errorMessage, $errorCode);
        }
} 