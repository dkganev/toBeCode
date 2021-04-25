<?php
	namespace Database;
	
	use PDO;

	class DBConnection {
		/**
		 * Stores the active connection
		 * @var PDO|null
		 */
		public $activeConnection = null;
		/**
		 * Instance of the connection
		 * @var DBConnection
		 */
		private static $instance = null;
		/**
		 * The constructing of the object is forbidden
		 * @access private
		 * @final
		 */
		final private function __construct() {}
		/**		
		 * Gets the active connection
		 * @return PDO|null The connection. If no connection is set returns null
		 */
		public function getActiveConnection(){
			return $this->activeConnection;
		}		
		/**
		 * Starts a transaction in the database
		 */
		public function startTransaction(){
			$this->getActiveConnection()->beginTransaction();
		}
		/**
		 * Rollbacks a transaction
		 */
		public function rollbackTransaction(){
			$this->getActiveConnection()->rollBack();
		}
		/**
		 * Commits a transaction
		 */
		public function doCommit(){
			$this->getActiveConnection()->commit();
		}
		/**
		 * Singleton design pattern
		 * @return Database\DBConnection
		 */
		public static function getInstance(){
			if(self::$instance == null){
				self::$instance = new DBConnection();
			}
			
			return self::$instance;
		}
		/**
		 * Changes the connection using connect method
		 * @param array $connectionParams
		 */
		public function setActiveConnection($connectionParams){
			$this->activeConnection = $this->connect($connectionParams);
		}
		
		/**
		 * Changes the active connection to new connection
		 * @param PDO $connection the connection instance
		 */
		public function changeActiveConnection(PDO $connection){
			$this->activeConnection = $connection;
		}
		/**
		 * Connect to database
		 * @param array $connectionParams
		 * @return PDO
		 */
		public function connect($connectionParams){
        	return new PDO($connectionParams['dbType']
                            .":host=".$connectionParams['dbHost']
                            .";port=".$connectionParams['dbPort']
                            .";dbname=".$connectionParams['dbName'], 
                            $connectionParams['dbUsername'], 
                            $connectionParams['dbPassword']
                        );
		}
	}