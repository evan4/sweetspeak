<?php


namespace controllers;

use models\UsersActions;



class LogController
{
    public function index($params = null)
    {
        UsersActions::makelog($params[0]['val']);
    }
}