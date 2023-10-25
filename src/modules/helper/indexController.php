<?php
class helper_indexController extends _system_controller
{
    public function assert()
    {
        $query = _request();
        $res = _assert($query['assert'] ?? '', $query['module'] ?? '');
        if (file_exists($res)) {
            http_response_code(200);
            $ext = pathinfo($res, PATHINFO_EXTENSION);
            $content_type = $this->content_type($ext);
            header("Content-Type: {$content_type}; charset=utf-8");
            readfile($res);
            exit;
        } else {
            http_response_code(404);
            //Set the content type to HTML (optional)
            header("Content-Type: text/plain; charset=utf-8");
            die('404');
        }
    }

    function content_type($ext)
    {
        $content_types = include _X_SYSTEM_DATA . '/content_types.php';

        return $content_types[$ext] ?? false;
    }
}
