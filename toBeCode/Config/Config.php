<?php
	namespace API;

	class Config {
		/**
		 * Holds Config instance 
		 * @var API\Config
		 * @staticvar
		 * @access private
		 */
		private static $instance = null;
		/**
		 * The configuration stack
		 * @var array key value pair with configurations
		 */
		private $configs = array();

		private $settings_configs = array();
		
		/**
		 * Object Constructor.
		 * @access private
		 */
		private function __construct(){
			$this->initConfigs("config.ini");
		//	$this->initSettingsConfigs(API_SETTINGS_PATH);
		}
		
		public function initConfigs($file){
			$this->configs = parse_ini_file($file);
		}

		public function initSettingsConfigs($file){
			$this->settings_configs = parse_ini_file($file);
		}

		/**
		 * Gets configuration or empty string if configuration is unknown
		 * @todo Set default value
		 * @param string $configName
		 * @return string configuration value
		 */
		public function getConfig($configName){
			return (isset($this->configs[$configName]))?$this->configs[$configName]:'';
		}

		public function getSettingsConfig($configName){
			return (isset($this->settings_configs[$configName]))?$this->settings_configs[$configName]:'';
		}
		/**
		* Gets whole configuration
		* @return array configurations
		*/
		public function getAllConfigs(){
			return (!empty($this->configs))?$this->configs:array();
		}
		/**
		 * Gets a set of configurations from array
		 * @param array $configArray
		 * @return array configurations
		 */
		public function getConfigArray(array $configArray){
			$return = array();
			foreach($configArray as $config){
				$return[$config] = $this->getConfig($config);
			}
			
			return $return;
		}
		/**
		 * Get the instance of Config
		 * @return MCS\Config
		 */
		public static function getInstance(){
			if(self::$instance == null){
				self::$instance = new Config();
			}
			
			return self::$instance;
		}
	}