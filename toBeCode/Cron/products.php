<?php
    require_once "../Config/init.php";

    use Config\ApiInit as ApiInit;
    use Database\DBStaticUtil;
    use Database\DBConnection;

    function addInnerJoin($catLevel,$catArray){
        $oldCatLevel = $catLevel++ ;
        $query = "INNER JOIN category AS cat$catLevel " 
                    ."ON cat$oldCatLevel.cat_id = cat$catLevel.cat_name_top_level_id "
                    . "AND cat$catLevel.cat_name = '$catArray[$oldCatLevel]' ";
        
        if (count($catArray) > $catLevel){
            $query .= addInnerJoin($catLevel,$catArray);
        }
        
        return $query; 
    }
    
    function ifCatPathExist($catPathArray){
        $query = 'SELECT * '
                .'FROM category as cat1 '
        ;
                
        if (count($catPathArray) > 1){
            $query .= addInnerJoin(1, $catPathArray); 
       }
                
        $query .= 'WHERE '
               . 'cat1.cat_name_top_level_id = 0 AND '
               . "cat1.cat_name = '$catPathArray[0]'"
        ;
        return DBStaticUtil::fetchAll($query);
    }
    
    function addCat($cat_name, $cat_name_top_level_id){
        $query = " INSERT INTO category (cat_name, cat_name_top_level_id) VALUES ('$cat_name',$cat_name_top_level_id)";
        
        return DBStaticUtil::insert($query);
    }
    

    $xml=simplexml_load_file("../Public/xmls/products.xml");
    
    foreach($xml->children() as $child1){
        echo "<hr>&nbsp;&nbsp;&nbsp;&nbsp;" . $child1->getName() . "<br>";
        foreach($child1->children() as $child2){
            switch ($child2->getName()) {
                case "cat_name":
                    echo"<br> cat_name";
                    $catPathArray = explode("|",strval($child1->prod_full_cat_path));
                    $catPathArray2 = explode("|",strval($child1->prod_full_cat));
                    $catPathArray2count = count($catPathArray2) - 1;
                    if (!in_array($catPathArray2[$catPathArray2count], $catPathArray)){
                        $catPathArray[] = $catPathArray2[$catPathArray2count];
                    }
                    $catPathArrayFin = $catPathArray;
                    $queryRess =  ifCatPathExist($catPathArray);
                    if(empty($queryRess)){
                        $catPathArrayCaunt = count($catPathArray) - 1;
                        $catPathArrayReverse = array();
                        for ($i = $catPathArrayCaunt; $i >= 0 ; --$i) {
                            $catPathArrayReverse[] = $catPathArray[$i];
                            unset($catPathArray[$i]);

                            $queryResult = ifCatPathExist($catPathArray);
                            if (empty($queryResult)) {
                                if ($i == 0){
                                    $cat_name_top_level_id = 0;
                                    for ($n = $catPathArrayCaunt; $n >= 0 ; --$n) {
                                        $queryResult = addCat($catPathArrayReverse[$n], $cat_name_top_level_id);
                                        $cat_name_top_level_id = $queryResult;
                                    }
                                    break;
                                }
                            }
                            else{
                                $cat_name_top_level_id = $queryResult[0]['cat_id'];
                                $catPathArrayReverseCaunt = count($catPathArrayReverse) - 1;
                                for ($n = $catPathArrayReverseCaunt; $n >= 0 ; --$n) {
                                    $queryResult = addCat($catPathArrayReverse[$n], $cat_name_top_level_id);
                                    $cat_name_top_level_id = $queryResult;
                                }
                                break;
                            }
                        };
                    }
                    
                    $queryRess =  ifCatPathExist($catPathArrayFin);
                    $cat_id = $queryRess[0]['cat_id'];
                    echo"<br> cat_name2";
                    break;
                case "propertyList":
                    //echo"<br> propertyList";
                    $propertyGroupRank = 0; 
                    foreach($child2->children() as $child3){
                        //id="1" sort_order="0" name="ПРОИЗВОДИТЕЛ" filter_use="1" slider="0" slider_measure="">
                        $propertyGroup_id = intval($child3['id']);
                        $products_id = intval(str_replace("code_","",strval($child1->products_id)));
                        $propertyGroupArray = [
                            'property_group_id' => $propertyGroup_id,
                            'sort_order' => intval($child3['sort_order']),
                            'group_name' => strval($child3['name']),
                            'filter_use' => intval($child3['filter_use']),
                            'slider' => intval($child3['slider']),
                            'slider_measure' => strval($child3['slider_measure']),
                            'property_group_rank' => $propertyGroupRank,
                        ];

                        //check and set if need products_to_group

                        $query = "SELECT * FROM products_to_group WHERE property_group_id = $propertyGroup_id AND products_id = $products_id ";
                        $queryResult = DBStaticUtil::fetchAll($query);
                        if (empty($queryResult)){
                            $sqlQuery = "INSERT INTO `products_to_group` (property_group_id, products_id) VALUES ($propertyGroup_id, $products_id)";
                            DBStaticUtil::insert($sqlQuery);
                        }
                        //check and set propertyGroup
                        $query = "SELECT * FROM propertyGroup WHERE property_group_id = $propertyGroup_id ";
                        $queryResult = DBStaticUtil::fetchAll($query);
                        // if propertyGroup exist
                        if (empty($queryResult)){
                            $columns = implode(", ",array_keys($propertyGroupArray));
                            $escaped_values = array_values($propertyGroupArray);
                            foreach ($escaped_values as $key=>$value) $escaped_values[$key] = "'$value'";
                            $values  = implode(", ", $escaped_values);

                            $sqlQuery = "INSERT INTO `propertyGroup`($columns) VALUES ($values)";
                            DBStaticUtil::insert($sqlQuery);
                        }  
                        else {
                        // if need update propertyGroup
                            if ($queryResult[0] != $propertyGroupArray){
                                $escaped_values2 = array();
                                foreach ($propertyGroupArray as $key=>$value)$escaped_values2[$key] = " $key = '$value'";
                                $values  = implode(", ", $escaped_values2);

                                $sqlQuery = "UPDATE `propertyGroup` SET $values  WHERE property_group_id = $propertyGroup_id ";
                                DBStaticUtil::query($sqlQuery);
                            }
                        }
                        //check and set propertyValue
                        echo"<br> propertyList2";
                        $propertyValueRank = 0; 
                        foreach($child3->children() as $child4){
                            $propertyValue_id = intval($child4['id']);
                            $propertyValue_name = strval($child4);
                                $propertyValueArray = [
                                    'property_value_id' => $propertyValue_id,
                                    'value_name' => $propertyValue_name,
                                    'property_value_rank' => $propertyValueRank,
                                ];

                                //check and set if need group_to_value
                                $query = "SELECT * FROM group_to_value WHERE property_group_id = $propertyGroup_id AND property_value_id = $propertyValue_id ";
                                $queryResult = DBStaticUtil::fetchAll($query);
                                if (empty($queryResult)){
                                    $sqlQuery = "INSERT INTO `group_to_value` (property_group_id, property_value_id) VALUES ($propertyGroup_id, $propertyValue_id)";
                                    DBStaticUtil::insert($sqlQuery);
                                }

                                //check and set property_value
                                $query = "SELECT * FROM property_value WHERE property_value_id = $propertyValue_id ";
                                $queryResult = DBStaticUtil::fetchAll($query);
                                // if property_value exist
                                echo"<br> propertyList3".$query;
                                if (empty($queryResult)){
                                    $columns = implode(", ",array_keys($propertyValueArray));
                                    $escaped_values = array_values($propertyValueArray);
                                    foreach ($escaped_values as $key=>$value) $escaped_values[$key] = "'$value'";
                                    $values  = implode(", ", $escaped_values);

                                    $sqlQuery = "INSERT INTO `property_value`($columns) VALUES ($values)";
                                echo"<br> propertyList4".$sqlQuery;
                                    DBStaticUtil::insert($sqlQuery);
                                echo"<br> propertyList5".$sqlQuery;
                                }  
                                else {
                                // if need update property_value
                                    if ($queryResult[0] != $propertyValueArray){
                                        $escaped_values2 = array();
                                        foreach ($propertyValueArray as $key=>$value)$escaped_values2[$key] = " $key = '$value'";
                                        $values  = implode(", ", $escaped_values2);

                                        $sqlQuery = "UPDATE `property_value` SET $values  WHERE property_value_id = $propertyValue_id ";
                                    echo"<br> propertyList6".$sqlQuery;
                                        DBStaticUtil::query($sqlQuery);
                                    echo"<br> propertyList7".$sqlQuery;}
                                }
                        $propertyValueRank += 1;    
                        }
                        $propertyGroupRank += 1;
                    }
                    echo"<br> propertyList4";
                    break;
                case "products_id":
                    echo"<br> products_id";
                    $products_id = intval(str_replace("code_","",strval($child1->products_id)));
                    $productArray = [
                        'products_id' => $products_id ,
                        'prod_name' => strval($child1->prod_name)  ,
                        'prod_name_english' => strval($child1->prod_name_english) ,
                        'prod_model' => strval($child1->prod_model) ,
                        'prod_descr_full' => strval($child1->prod_descr_full) ,
                        'Price_EndUser_LV_wo_VAT' => floatval($child1->Price_EndUser_LV_wo_VAT) ,
                        'prod_descr' => strval($child1->prod_descr) ,
                        'prod_avail' => strval($child1->prod_avail) == 'Y' ? 1:0 ,
                        'prod_weight' => floatval($child1->prod_weight) ,
                        'prod_warra' => strval($child1->prod_warra) ,
                        'prod_full_cat_path' => strval($child1->prod_full_cat_path) ,
                        'prod_full_cat' => strval($child1->prod_full_cat) ,
                        'prod_descr_text' => strval($child1->prod_descr_text) ,
                        'video_reviews' => strval($child1->video_reviews) ,
                        'EAN' => strval($child1->EAN) ,
                        'In_Promotion' => strval($child1->In_Promotion) == 'Y' ? 1:0 ,
                        'manufacturers_id' => intval($child1->manufacturers_id) ,
                        'cat_id' => $cat_id ,
                    ];
                    
                    $query = "SELECT * FROM products WHERE products_id = $products_id ";
                    $queryResult = DBStaticUtil::fetchAll($query);

                    // if products_id not exist
                    if (empty($queryResult)){
                        $columns = implode(", ",array_keys($productArray));
                        $escaped_values = array_values($productArray);
                        foreach ($escaped_values as $key=>$value) $escaped_values[$key] = "'".$value."'";
                        $values  = implode(", ", $escaped_values);

                        $sqlQuery = "INSERT INTO `products`($columns) VALUES ($values)";
                        DBStaticUtil::insert($sqlQuery);
                    }  
                    else {
                    // if need update product
                        if ($queryResult[0] != $productArray){
                            foreach ($productArray as $key=>$value)$escaped_values[$key] = $key . " = '" .  $value . "'";
                            $values  = implode(", ", $escaped_values);

                            $sqlQuery = "UPDATE `products` SET $values  WHERE products_id = $products_id ";
                            DBStaticUtil::query($sqlQuery);
                        }
                    }
                    echo"<br> products_id2";
                    break;
                case "manufacturers_id":
                    echo"<br> manufacturers_id";
//                  echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $child2->getName() . ": " . $child2 . "<br>";
                    $manufacturers_id = intval($child1->manufacturers_id);
                    $man_name = strval($child1->man_name);
                    $manufacturersArray = [
                        'manufacturers_id' => $manufacturers_id,
                        'man_name' => $man_name,
                    ];
//                    print_r($manufacturersArray);
                    $query = "SELECT * FROM manufacturers WHERE manufacturers_id = $manufacturers_id ";
                    $queryResult = DBStaticUtil::fetchAll($query);

                    // if manufacturers_id exist
                    if (empty($queryResult)){
                        $sqlQuery = "INSERT INTO `manufacturers`(manufacturers_id, man_name) VALUES ($manufacturers_id, '$man_name' )";
                        DBStaticUtil::insert($sqlQuery);
                    }  
                    else {

                    // if need update manufacturers
                        if ($queryResult[0] != $manufacturersArray){
                            $sqlQuery = "UPDATE `manufacturers` SET manufacturers_id = $manufacturers_id, man_name = '$man_name'  WHERE manufacturers_id = $manufacturers_id";
                            DBStaticUtil::query($sqlQuery);
                        }
                    }
//                    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $child2->getName() . ": " . $child2 . "<br>";
                    echo"<br> manufacturers_id2";
//                  break;
                
                case "prod_name":
                case "prod_name_english":
                case "prod_model":
                case "prod_descr_full":
                case "Price_EndUser_LV_wo_VAT":

                case "prod_descr":
                case "prod_avail":
                case "prod_weight":
                case "prod_warra":
                case "prod_full_cat_path":

                case "prod_full_cat":
                case "prod_descr_text":
                case "video_reviews":
                case "EAN":
                case "In_Promotion":

                case "man_name":
                case "cat_name_top_level":    
                    break;
                default:
                    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $child2->getName() . ": " . $child2 . "<br>";
                }

        }
    }

?>