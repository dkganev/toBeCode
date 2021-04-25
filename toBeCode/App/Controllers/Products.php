<?php
    namespace Controllers;
    require_once "init.php";
    
    use Database\DBStaticUtil;
    use Database\DBConnection;
    
   
    
    
    class Products {
		
        public function __construct(){
                
        }
            
            
            
        public function Index(){
            $offset = $_POST['offset'];
            $limit = $_POST['limit'];
            
            $query = "SELECT * "
                    . "FROM "
                        . "products as p, "
                        . "category as c,"
                        . "manufacturers as m"
                    . " WHERE "
                        . "p.cat_id = c.cat_id AND "
                        . "p.manufacturers_id = m.manufacturers_id "
                    . "LIMIT $limit  OFFSET $offset ";
            
            $result = array();            
            $result = DBStaticUtil::fetchAll($query);
            
            foreach($result as $key => &$value){
                if($value['prod_avail'] == 1){
                    $value['prod_avail'] = 'Yes';
                }
                else {
                    $value['prod_avail'] = 'No';
                }
                
                if($value['In_Promotion'] == 1){
                    $value['In_Promotion'] = 'Yes';
                }
                else {
                    $value['In_Promotion'] = 'No';
                }
                
                $action = '<button type="button" onclick="openViewForm('.$value['products_id'].')" class="btn btn-success" data-dismiss="view">View <i class="fa fa-eye"></i></button>';
                          
                
                $value['action'] = $action;        
                
            }
            
            
            
            $query = "SELECT COUNT(products_id) AS count FROM products ";
            $countRes = DBStaticUtil::fetchAll($query);
            $count = $countRes[0]['count']; 
            
            $data = [
                "total" => $count,
                "totalNotFiltered" =>  $count,
                "rows" => $result
            ];
            
            echo json_encode(['success' => 1, 'data' => $data ]);
            return ;
                
                
        }
    }
     
    Products::Index();
    
    
    
?>