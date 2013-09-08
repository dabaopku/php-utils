<?phpclass Validation{	static function authorize()	{	}		static function required($params)	{		if(!is_array($params))			$params = array($params);				foreach ($params as $p)		{			if(!isset($_REQUEST[$p]))			{				Json::generateResult(false, "$p requried");			}		}	}		static function getUser($param = "id")	{		$id = Parser::int($_REQUEST[$param]);		$user = User::model()->findByPk($id);		if(!$user)		{			Json::generateResult(false, ErrorCode::USER_NOT_EXIST);		}		return $user;	}		static function getCircle($param = "id")
	{
		$id = Parser::int($_REQUEST[$param]);
		$circle = Circle::model()->findByPk($id);
		if(!$circle)
		{
			Json::generateResult(false, ErrorCode::CIRCLE_NOT_EXIST);
		}
		return $circle;
	}}