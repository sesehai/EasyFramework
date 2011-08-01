<?php
class Factory
{
	protected static $_instances = array();

	public static function instance($driver)
	{
		if (empty(self::$_instances[$driver]))
		{
			$class = 'Plog_Handler_'.strtoupper($driver);
			require(ROOT_PATH .'/core/log/'.strtolower(str_replace('_', DIRECTORY_SEPARATOR, $class)).'.php');
			$class = 'Plog_Handler_'.$driver;
			self::$_instances[$driver] = new $class($driver);
		}

		return self::$_instances[$driver];
	}
}
?>