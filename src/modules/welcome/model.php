<?php
class welcome_model
{

    function cors()
    {
        _request('before', 'cors');
        return ['return from before handle'=>__DIR__];
    }

    function json()
    {
        _request('after', 'json');
        return ['return from after handle' => __FUNCTION__]; 
    }
}