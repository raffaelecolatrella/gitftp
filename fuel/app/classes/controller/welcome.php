<?php

class Controller_Welcome extends Controller {

    public function action_index() {
        $view = View::forge('layout/base_layout.mustache');
        $view->css = View::forge('layout/css');
        $view->js = View::forge('layout/js');
        $view->header = View::forge('layout/header');
        $view->footer = View::forge('layout/footer');
        $view->body = View::forge('page/home');
        return $view;
//        $a = DB::select()->from('users')->execute()->as_array();
//        print_r($a);
    }

    public function action_testmulti() {
        echo 'asdas';
    }

}
