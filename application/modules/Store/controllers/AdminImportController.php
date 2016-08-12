<?php

class Store_AdminImportController extends Core_Controller_Action_Admin {

    public function indexAction() {

        $table = Engine_Api::_()->getDbtable('products', 'store');
        $this->view->products = $products = $table->getAllProducts($this->_getParam('itemCountPerPage'));

        $api = Engine_Api::_()->getApi('page', 'store');
        $paginator = $api->getAllStores()->toArray();
        $this->view->category = $paginator;

        // Do not render if nothing to show
        if( count($products) <= 0 ) {
            return;
        }

    }

    public function exportAction(){

        $ids = explode(',',$this->_getParam('ids'));

        $array_for_select = array();
        foreach($ids as $id){
            array_push($array_for_select,'"'.$this->_getParam('product_id_'.$id).'"');
        }

        $table2 = Engine_Api::_()->getDbtable('products', 'store');
        $result = $table2->select()->where('product_id IN('.implode(',',$array_for_select).')');

        $fin = $table2->fetchAll($result);

        $string = "";
        $temporary = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary' . DIRECTORY_SEPARATOR;


        $myfile = fopen($temporary ."export.csv", "w");

        foreach($fin as $item){
            $string.=str_replace(',','',strip_tags($item['title'])).','.str_replace(',','',strip_tags($item['price'])).','.str_replace(',','',strip_tags($item['description'])).','.str_replace(',','',strip_tags($item['quantity']))."\n";
            fwrite($myfile, $string);
        }
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename="export.csv"');
        readfile($temporary ."export.csv");
        exit;
    }

    public function importAction(){

        if(is_uploaded_file($_FILES["load"]["tmp_name"]))
        {
            $handle= (file_get_contents($_FILES['load']['tmp_name'],"r"));

            $result = explode("\n",$handle);

            $category = $this->_getParam('category');

            $viewer = Engine_Api::_()->user()->getViewer();

            $table = Engine_Api::_()->getDbtable('products', 'store');

                foreach($result as $item){
                    $row = (explode(',',$item));
                    if(count($row)<2){continue;};
                    $description =  $row[2];
                    if(!$description){
                        $description='';
                    }
                    $quantity = $row[3];
                    if(!$quantity){
                        $quantity=1;
                    }
                    $action = $table->createRow();
                    $rows =array(
                        'title' => $row[0],
                        'description' => $description,
                        'price' => $row[1],
                        'page_id' => $category,
                        'quantity' => $quantity
                    );

                    if($category==0){
                        $rows['owner_id']= $viewer->getIdentity();
                    }

                    $action->setFromArray($rows);
                    $action->save();
                }
            $this->redirect('admin/store/import');
        } else {
            echo("Error upload file");
        }

    }




}