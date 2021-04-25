<?php
	namespace Database;
	use PDO;

	class DBStaticUtil {
		/**
		 * Executes a query string to the database.
		 * @param $statement query string
		 * @return PDOStatement
		 * @throws QueryException if query execution failed.
		 */
		public static function query($statement){
			$db_connection = self::getConnection();
			$pdoStatement = $db_connection->prepare($statement);
			
			$return = $pdoStatement->execute();
			if(!$return){
				dump($pdoStatement->errorInfo());
				throw new \Exception('Query ['.$statement.'] FAILED');
			}
			
			return $pdoStatement;
		}
		/**
		 * Gets the active database connection
		 * @access private
		 * @static
		 * @return PDO the active connection
		 */
		private static function getConnection(){
			return  DBConnection::getInstance()->getActiveConnection();
		}
		/**
		 * Starts a transaction
		 * @static 
		 */
		public static function startTransaction(){
			self::getConnection()->beginTransaction();
		}
		/**
		 * Commits a transaction
		 * @static 
		 */
		public static function commitTransaction(){
			self::getConnection()->commit();
		}
		/**
		 * Rollbacks a transaction
		 * @static 
		 */
		public static function rollbackTransaction(){
			self::getConnection()->rollBack();
		}
		/**
		 * Executes a query and fetch a signe result in associative array
		 * @param string $query The query string
		 * @return array
		 * @static 
		 */
		public static function fetch($query){
			$query_statement =  self::query($query);
			return $query_statement->fetch(PDO::FETCH_ASSOC);
		}
		/**
		 * Executes a query and fetch all results in associative array 
		 * @param string $query The query string
		 * @return array 
		 * @static
		 */
		public static function fetchAll($query){
			$query_statement =  self::query($query);
			return $query_statement->fetchAll(PDO::FETCH_ASSOC);
		}
                
                public static function ifExist($query){
			$query_statement =  self::fetchAll($query);
                        //return $query_statement;
                        if (empty($query_statement)){
                            return FALSE;
                        }
                        else {
                            return TRUE;
                        //    return $query_statement;
                        }
		}
                public static function insert($statement){
			$db_connection = self::getConnection();
			$pdoStatement = $db_connection->prepare($statement);
			
			$return = $pdoStatement->execute();
			if(!$return){
				dump($pdoStatement->errorInfo());
				throw new \Exception('Query ['.$statement.'] FAILED');
			}
			return $db_connection->lastInsertId();
		}
		
                
                
                
	}