<?php

class Json
{
	static function encode($var)
	{
		switch (gettype($var)) {
			case 'boolean':
				return $var ? 'true' : 'false';
	
			case 'NULL':
				return 'null';
	
			case 'integer':
				return (int) $var;
	
			case 'double':
			case 'float':
				return str_replace(',','.',(float)$var); // locale-independent representation
	
			case 'string':
				if (($enc=strtoupper(Yii::app()->charset))!=='UTF-8')
					$var=iconv($enc, 'UTF-8', $var);
	
				// STRINGS ARE EXPECTED TO BE IN ASCII OR UTF-8 FORMAT
				$ascii = '';
				$strlen_var = strlen($var);
	
				/*
				 * Iterate over every character in the string,
				* escaping with a slash or encoding to UTF-8 where necessary
				*/
				for ($c = 0; $c < $strlen_var; ++$c) {
	
					$ord_var_c = ord($var{$c});
	
					switch (true) {
						case $ord_var_c == 0x08:
							$ascii .= '\b';
							break;
						case $ord_var_c == 0x09:
							$ascii .= '\t';
							break;
						case $ord_var_c == 0x0A:
							$ascii .= '\n';
							break;
						case $ord_var_c == 0x0C:
							$ascii .= '\f';
							break;
						case $ord_var_c == 0x0D:
							$ascii .= '\r';
							break;
	
						case $ord_var_c == 0x22:
						case $ord_var_c == 0x2F:
						case $ord_var_c == 0x5C:
							// double quote, slash, slosh
							$ascii .= '\\'.$var{$c};
							break;
	
						default:
							$ascii .= $var{$c};
							break;
					}
				}
	
				return '"'.$ascii.'"';
	
			case 'array':
				/*
				 * As per JSON spec if any array key is not an integer
				 * we must treat the the whole array as an object. We
				 * also try to catch a sparsely populated associative
				 * array with numeric keys here because some JS engines
				 * will create an array with empty indexes up to
				 * max_index which can cause memory issues and because
				 * the keys, which may be relevant, will be remapped
				 * otherwise.
				 *
				 * As per the ECMA and JSON specification an object may
				 * have any string as a property. Unfortunately due to
				 * a hole in the ECMA specification if the key is a
				 * ECMA reserved word or starts with a digit the
				 * parameter is only accessible using ECMAScript's
				 * bracket notation.
				 */
	
				// treat as a JSON object
				if (is_array($var) && count($var) && (array_keys($var) !== range(0, sizeof($var) - 1))) {
					return '{' .
							join(',', array_map(array('Json', 'nameValue'),
									array_keys($var),
									array_values($var)))
									. '}';
				}
	
				// treat it like a regular array
				return '[' . join(',', array_map(array('Json', 'encode'), $var)) . ']';
	
			case 'object':
				if ($var instanceof Traversable)
				{
					$vars = array();
					foreach ($var as $k=>$v)
						$vars[$k] = $v;
				}
				else
					$vars = get_object_vars($var);
				return '{' .
						join(',', array_map(array('Json', 'nameValue'),
								array_keys($vars),
								array_values($vars)))
								. '}';
	
			default:
				return '';
		}
	}
	
	private static function nameValue($name, $value)
	{
		return self::encode(strval($name)) . ':' . self::encode($value);
	}
	
	static function generateResult($success, $result=NULL)
	{
		header('Content-Type: application/json');
		echo self::encode(array('success'=>$success,'result'=>$result));
		Yii::app()->end();
	}
}
