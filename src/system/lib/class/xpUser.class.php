<?php


class xpUser {


    protected $name='user'; //e.g. Admin, super, 
    /**
     * source methods:
     * 
     *  - create
     *  - suspend
     *  - remove
     *  - update
     *  - password
     *  - auth
     *  - current
     * getBy:email/id/name
     * 
     */
    protected $source;
    function __construct($source) 
    {
        $this->source = $source;
    }
    public function auth() 
    {

    }

    public function set_source($source) 
    {
        $this->source = $source;
    }


    public function current()
    {

    }
   
    public function create()
    {

    }
    //delete
    public function suspend()
    {

    }
    public function activate(){}

    public function remove(){}
    public function update(){}
    public function validate(){}
    public function password(){}
    public function password_update(){}




}