<?php
	{
		$id = Parser::int($_REQUEST[$param]);
		$circle = Circle::model()->findByPk($id);
		if(!$circle)
		{
			Json::generateResult(false, ErrorCode::CIRCLE_NOT_EXIST);
		}
		return $circle;
	}