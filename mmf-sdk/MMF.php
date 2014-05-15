<?php

include_once "MMF_OAuth.php";
include_once "MMF_Config.php";

class MMF {

	function getAuthenticatedUser($accessToken, $accessTokenSecret) {
		$store = MMF::createOAuthStore($accessToken, $accessTokenSecret);
		$request = new OAuthRequester(
			"https://api.mapmyapi.com/v7.0/user/self/",
			'GET',
			array()
		);
		return MMF::getRequestResults($request);
	}

	function getAuthenticatedUserId($accessToken, $accessTokenSecret) {
		$authenticatedUser = MMF::getAuthenticatedUser($accessToken, $accessTokenSecret);
		return $authenticatedUser["id"];
	}

	function getUserById($accessToken, $accessTokenSecret, $userId) {
		$store = MMF::createOAuthStore($accessToken, $accessTokenSecret);
		$request = new OAuthRequester(
			"https://api.mapmyapi.com/v7.0/user/" . $userId . "/",
			'GET',
			array()
		);
		return MMF::getRequestResults($request);
	}

	function getFriendsOfUser($accessToken, $accessTokenSecret, $userId) {
        $store = MMF::createOAuthStore($accessToken, $accessTokenSecret, $userId);
        $request = new OAuthRequester(
            "https://api.mapmyapi.com/v7.0/user/?friends_with=" . $userId . "&limit=10" ,
            'GET',
            array()
        );
        $results = MMF::getRequestResults($request);
        return $results["_embedded"]["user"];
    }

	function getUserProfilePicture($accessToken, $accessTokenSecret, $userId) {
		$store = MMF::createOAuthStore($accessToken, $accessTokenSecret);
		$request = new OAuthRequester(
			"https://api.mapmyapi.com/v7.0/user_profile_photo/" . $userId . "/",
			'GET',
			array()
		);
		$result = MMF::getRequestResults($request);
		return array(
			"small" => $result["_links"]["small"][0]["href"],
			"medium" => $result["_links"]["medium"][0]["href"],
			"large" => $result["_links"]["large"][0]["href"]
		);
	}

	function getUserStats($accessToken, $accessTokenSecret, $userId, $aggregateByPeriod) {
		$store = MMF::createOAuthStore($accessToken, $accessTokenSecret);
		$request = new OAuthRequester(
			"https://api.mapmyapi.com/v7.0/user_stats/" . $userId . "/",
			'GET',
			array(
				"aggregate_by_period" => $aggregateByPeriod
			)
		);
		$result = MMF::getRequestResults($request);
		return $result["_embedded"]["stats"];
	}

	function getUserAchievements($accessToken, $accessTokenSecret, $userId) {
		$store = MMF::createOAuthStore($accessToken, $accessTokenSecret);
		$request = new OAuthRequester(
			"https://api.mapmyapi.com/v7.0/user_achievement/",
			'GET',
			array(
				"user" => $userId
			)
		);
		$result = MMF::getRequestResults($request);
		return $result["_embedded"]["user_achievement"];
	}

	function getWorkoutsForUser($accessToken, $accessTokenSecret, $userId) {
		$store = MMF::createOAuthStore($accessToken, $accessTokenSecret);
		$request = new OAuthRequester(
			"https://api.mapmyapi.com/v7.0/workout/",
			'GET',
			array(
				"user" => "/v7.0/user/" . $userId . "/"
			)
		);
		$result = MMF::getRequestResults($request);
		return $result["_embedded"]["workouts"];
	}

	function getActivityType($accessToken, $accessTokenSecret, $activityTypeId) {
		$store = MMF::createOAuthStore($accessToken, $accessTokenSecret);
		$request = new OAuthRequester(
			"https://api.mapmyapi.com/v7.0/activity_type/" . $activityTypeId . "/",
			'GET',
			array()
		);
		return MMF::getRequestResults($request);
	}

	function getActivityTypes($accessToken, $accessTokenSecret) {
		$store = MMF::createOAuthStore($accessToken, $accessTokenSecret);
		$request = new OAuthRequester(
			"https://api.mapmyapi.com/v7.0/activity_type/",
			'GET',
			array()
		);
		return MMF::getRequestResults($request);
	}

	function getWorkoutById($accessToken, $accessTokenSecret, $workoutId) {
		$store = MMF::createOAuthStore($accessToken, $accessTokenSecret);
		$request = new OAuthRequester(
			"https://api.mapmyapi.com/v7.0/workout/" . $workoutId . "/",
			'GET',
			array()
		);
		return MMF::getRequestResults($request);
	}

	function getCourseById($accessToken, $accessTokenSecret, $courseId, $thumbnailHeight, $thumbnailWidth) {
        $store = MMF::createOAuthStore($accessToken, $accessTokenSecret);
        $request = new OAuthRequester(
            "https://api.mapmyapi.com/v7.0/course/" . $courseId . "/",
            'GET',
			array(
                "thumbnail" => "True",
				"thumbnail_height" => $thumbnailHeight,
				"thumbnail_width" => $thumbnailWidth
				)
        );
        $result = MMF::getRequestResults($request);
        return $result;
    }

    function getCourseLeaderBoard($accessToken, $accessTokenSecret, $courseId, $activityTypeID) {
        $store = MMF::createOAuthStore($accessToken, $accessTokenSecret);
        $request = new OAuthRequester(
            "https://api.mapmyapi.com/api/0.1/course_leaderboard/" . $courseId . "/",
            'GET',
            array(
                "activity_type_id" => $activityTypeID
            )
        );
        $result = MMF::getRequestResults($request);
        return $result["standings"]["entries"];
    }

    function getCourseMap($accessToken, $accessTokenSecret, $courseId) {
        $store = MMF::createOAuthStore($accessToken, $accessTokenSecret);
        $request = new OAuthRequester(
            "https://api.mapmyapi.com/api/0.1/course_map/" . $courseId . "/",
            'GET'
        );
        $result = MMF::getRequestResults($request);
        return $result;
    }

	function getRoute($accessToken, $accessTokenSecret, $routeId) {
		$store = MMF::createOAuthStore($accessToken, $accessTokenSecret);
		$request = new OAuthRequester(
			"https://api.mapmyapi.com/v7.0/route/" . $routeId . "/",
			'GET',
			array(
				"field_set" => "detailed"
			)
		);
		$result = MMF::getRequestResults($request);
		return $result;
	}

	function getBookmarkedRoutesForUser($accessToken, $accessTokenSecret, $userId) {
        $store = MMF::createOAuthStore($accessToken, $accessTokenSecret);
        $request = new OAuthRequester(
            "https://api.mapmyapi.com/v7.0/route_bookmark/",
            'GET',
            array(
                "user" => $userId
            )
        );
        $result = MMF::getRequestResults($request);
        return $result['_embedded']['route_bookmarks'];
    }

	/**
	 * Creates a new OAuthStore and adds Access Tokens so that we can perform other oauth-php library functions.
	 * @param  string $accessToken        	OAuth Access Token
	 * @param  string $accessTokenSecret 	OAuth Access Token
	 * @return OAuthStore 					Returns the OAuth Store object that was created
	 */
	protected function createOAuthStore($accessToken, $accessTokenSecret) {
		$store = MMF_OAuth::setOAuthStore();
		$store->addServerToken(MMF_OAUTH_CONSUMER_SECRET, 'access', $accessToken, $accessTokenSecret, 0);

		return $store;
	}

	/**
	 * Performs the Signed OAuth Request with appropriate error handling
	 * @param  OAuthRequester $requestPredefined 	Request parameters using the OAuthRequester object
	 * @param  boolean		  $keepLinks 			Set to true if you want to keep the "_links" subarray
	 * @return array 								Deserialized PHP Array from the returned JSON object from the server
	 */
	protected function getRequestResults($request, $removeLinks = false) {
		try {
			$result = $request->doRequest(1);
			$decodedResult = json_decode($result["body"], true);
		} catch (Exception $e) {
			throw new MMFSDKException($e->getMessage(), $e->getCode(), $request);
		}
		// Removes the links array. This array contains links to other Endpoints
		if ($removeLinks) unset($decodedResult["_links"]);
		return $decodedResult;
	}
}

class MMFSDKException extends Exception
{
	private $_request;

    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, $requestIn, Exception $previous = null) {
        parent::__construct($message, $code, $previous);

        $this->_request = $requestIn;
    }

    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function getHTTPMessage() {
    	return substr($this->_message, 0, strpos($exceptionMessage, '{'));
    }

    public function getDiagnostics() {
    	return substr($this->_message, strpos($exceptionMessage, '{'), strlen($exceptionMessage));
    }

    public function getRequest() {
        return $this->_request;
    }
}

?>