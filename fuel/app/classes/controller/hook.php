<?php

class Controller_Hook extends Controller {

    public function action_index() {
        echo '';
    }
    
    public function action_i($user_id = null, $deploy_id = null, $key = null){
        
        if($user_id == null || $deploy_id == null || $key == null){
            echo '404 brother';
            return ;
        }
        
        
        
        
        DB::insert('test')->set(array(
            'test' => serialize($_REQUEST['payload'])
        ))->execute();
//        if(Input::method() != 'POST'){
//            
//        }
    }
    
    public function action_get(){
        echo '<pre>';
        $a = DB::select()->from('test')->execute()->as_array();
        print_r(json_decode(unserialize($a[1]['test'])));
    }
}
