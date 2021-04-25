<?php
    namespace Controllers;
    require_once "init.php";
    
    use Database\DBStaticUtil;
    use Database\DBConnection;
    
   
    
    
    class OpenViewForm {
		
        public function __construct(){
                
        }
            
            
            
        public function Index(){
            $products_id = $_POST['products_id'];
            
            
            $query = "SELECT * "
                    . "FROM "
                        . "products as p, "
                        . "category as c, "
                        . "manufacturers as m "
                    . "WHERE "
                        . "p.manufacturers_id = m.manufacturers_id AND "
                        . "p.cat_id = c.cat_id AND "
                        . "p.products_id = $products_id ";
            
            $result = array();            
            $result = DBStaticUtil::fetchAll($query);
            $result = $result[0];
            
            if($result['prod_avail'] == 1){
                $result['prod_avail'] = 'Yes';
            }
            else {
                $result['prod_avail'] = 'No';
            }
                
            if($result['In_Promotion'] == 1){
                $result['In_Promotion'] = 'Yes';
            }
            else {
                $result['In_Promotion'] = 'No';
            }
            
            
            
            
            
            echo json_encode(['success' => 1, 'data' => $result ]);
            return ;
                
                
        }
    }
     
    OpenViewForm::Index();
    
    
    
?>