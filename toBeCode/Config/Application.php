<?php
	namespace API;

	use Database\DBConnection;

	class Application {
		/**
		 * Holds Modules instances.
		 * @var Module []
		 */
		private $modules = array();
		/**
		 * Starts the browser Session
		 * @todo set Custom Session Browser if needed
		 */
		public function startSession(){
			session_start();
		}
		
		/**
		 * Inits the database connection.
		 */
		public function initDb(){
			$database = Config::getInstance()->getConfigArray(array(
				'dbType',
				'dbPort',
				'dbHost',
				'dbName',
				'dbUsername',
				'dbPassword',
			));
			//return $database;
			DBConnection::getInstance()->setActiveConnection($database);
		}
		/**
		 * Object constructor
		 * @return API\Application
		 */
		public function __construct(){
			return $this;
		}
		/**
		 * Inits the alternative path of api
		 */
		public function initPaths(){
			$errors = Config::getInstance()->getConfig('display_errors');
			$timezone = Config::getInstance()->getConfig('timezone');
			date_default_timezone_set($timezone);
			$pathAlternative = Config::getInstance()->getConfig('path_alternative');
			@$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
			define("PATH_ALTERNATIVE", "/".$pathAlternative);
			define('DISPLAY_ERRORS', $errors);
			define('MAIN_PATH', $protocol. @$_SERVER['SERVER_NAME'] . "/" . $pathAlternative);
		}
	}