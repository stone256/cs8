<?php
class welcome_model
{

    public function cors()
    {
        return ['before handle1 add' => "file =>" . str_replace(_X_ROOT, '', __FILE__)];
    }

    public function json()
    {
        return ['after handle add' => "file =>" . str_replace(_X_ROOT, '', __FILE__)];
    }
}
