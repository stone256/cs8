<?php
class welcome_example_model_one
{

    public function show()
    {
        return str_replace(_X_MODULE, '',  __FILE__);
    }
}
