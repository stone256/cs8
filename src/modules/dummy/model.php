<?php

class dummy_model
{
    function name()
    {
        return md5(uniqid());
    }
}
