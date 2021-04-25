<?php
    namespace Config;
    
    use Database\DBConnection;
        
    class ApiInit {
        private $configs = array();
        
        private static $instance = null;

        function __construct($file){
            $this->initConfigs($file);
        }

        public function initConfigs($file){
            $this->configs = parse_ini_file($file);
        }

        public function getConfig($configName){
            return (isset($this->configs[$configName]))?$this->configs[$configName]:'';
        }

        public function getAllConfigs(){
            return (!empty($this->configs))? $this->configs : array();
        }
        
        public static function getInstance(){
            if(self::$instance == null){
		self::$instance = new ApiInit();
            }
			
            return self::$instance;
	}
        
        	
        
        
        

    }
?>

   
        
