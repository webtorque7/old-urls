<?php
/**
 * Need to overwrite the httpError function
 */

class OldURL_ModelAsController extends ModelAsController
{

//        /**
//         * Don't pass errorCode if response is a redirect
//         *
//         * @param int $errorCode
//         * @param string $errorMessage Plaintext error message
//         * @uses SS_HTTPResponse_Exception
//         */
//        public function httpError($errorCode, $errorMessage = null) {
//		Debug::dump($errorCode);exit();
//                // Call a handler method such as onBeforeHTTPError404
//                $this->extend('onBeforeHTTPError' . $errorCode, $this->request);
//
//                // Call a handler method such as onBeforeHTTPError, passing 404 as the first arg
//                $this->extend('onBeforeHTTPError', $errorCode, $this->request);
//
//                if ($errorMessage instanceof SS_HTTPResponse && $errorMessage->getHeader('Location')) {
//                        throw new SS_HTTPResponse_Exception($errorMessage, $errorMessage->getStatusCode());
//                }
//                // Throw a new exception
//                throw new SS_HTTPResponse_Exception($errorMessage, $errorCode);
//        }

	/**
	 * Get the appropriate {@link ContentController} for handling a {@link SiteTree} object, link it to the object and
	 * return it.
	 *
	 * @param SiteTree $sitetree
	 * @param string $action
	 * @return ContentController
	 */
	static public function controller_for(SiteTree $sitetree, $action = null)
	{
		if ($sitetree->class == 'SiteTree') $controller = "ContentController";
		else $controller = "{$sitetree->class}_Controller";

		if ($action && class_exists($controller . '_' . ucfirst($action))) {
			$controller = $controller . '_' . ucfirst($action);
		}

		return class_exists($controller) ? Injector::inst()->create($controller, $sitetree) : $sitetree;
	}

	/**
	 * @uses ModelAsController::getNestedController()
	 * @return SS_HTTPResponse
	 */
	public function handleRequest(SS_HTTPRequest $request, DataModel $model)
	{
		$this->request = $request;
		$this->setDataModel($model);

		$this->pushCurrent();

		// Create a response just in case init() decides to redirect
		$this->response = new SS_HTTPResponse();

		$this->init();

		// If we had a redirection or something, halt processing.
		if ($this->response->isFinished()) {
			$this->popCurrent();
			return $this->response;
		}

		// If the database has not yet been created, redirect to the build page.
		if (!DB::isActive() || !ClassInfo::hasTable('SiteTree')) {
			$this->response->redirect(Director::absoluteBaseURL() . 'dev/build?returnURL=' . (isset($_GET['url']) ? urlencode($_GET['url']) : null));
			$this->popCurrent();

			return $this->response;
		}

		try {

			$oldURL = $this->handleOldURL($request);


			if ($oldURL instanceof SS_HTTPResponse) {
				return $oldURL;
			}
			else if ($oldURL === true) {
				return;
			}

			$result = $this->getNestedController();

			if ($result instanceof RequestHandler) {
				$result = $result->handleRequest($this->request, $model);
			} else if (!($result instanceof SS_HTTPResponse)) {
				user_error("ModelAsController::getNestedController() returned bad object type '" .
					get_class($result) . "'", E_USER_WARNING);
			}
		} catch (SS_HTTPResponse_Exception $responseException) {
			$result = $responseException->getResponse();
		}

		$this->popCurrent();
		return $result;
	}

	public function handleOldURL($request) {
		$url = $request->getURL();
		$action = '';

		$oldURLRedirectOBJ = OldURLRedirect::get_from_url(str_replace(' ', '%20', $url));

		//if not found, try removing action
		if (!$oldURLRedirectOBJ) {
			$slash = strrpos('/', $url);
			$url = substr($url, 0, $slash);
			$action = substr($url, $slash + 1);
		}

		if ($oldURLRedirectOBJ) {
			$redirectPage = $oldURLRedirectOBJ->Page();
			$dontRedirect = $oldURLRedirectOBJ->DontRedirect;

			global $oldURLRedirected;
			$oldURLRedirected = true;

			if ($dontRedirect) {
				//@todo handle actions (forms etc)
				Director::direct(Controller::join_links($redirectPage->Link(), $action), new DataModel());
				return true;
			} else {
				return $oldURLRedirectOBJ->redirection(self::controller_for($redirectPage));
			}
		}

		return false;
	}
} 