<?php
class sitemin_examplesController extends _system_controller
{
    /**
     * example of batch  method
     */
    function batch()
    {

        $q = $this->query;

        if ($q['save'] ?? false) { //ajax call
            $batch = array(
                'start' => $q['index'],
                'total' => $q['total'],
                'size' => $q['size'],
            );

            $batch = helper::batch($batch);
            $batch['msg'] = "block : " . ($batch['start'] + 1) . " - " . ($batch['end'] + 1) . " of {$batch['total']}";

            sleep(1);
            die(json_encode($batch));
        }
        $res = $q;
        $res['TITLE'] = 'SITEMIN EXAMPLE BATCH';
        $res['NAME'] = 'batch';
        $res['route'] = routing()->matched();

        $res['tpl'] = $res['route']['view'] . '/example/batch.phtml';
        // or hardcoded
        //$res['tpl'] = _X_MODULE . '/sitemin/view/login/dashboard.phtml';

        return ['view' => $res['route']['view'] . '/index.phtml', 'data' => $res];
    }
    /**** batch testing ***/


    function blade()
    {
        // $blade = new examples_blade(_X_MODULE . '/sitemin/view/example/blade/');
        $blade = _factory('sitemin_model_blade', [_X_MODULE . '/sitemin/view/example/blade/']);
        $blade->run('sub/bar', null, array('controller' => __CLASS__ . "::" . __FUNCTION__));
        exit;
    }





    function indexAction()
    {
        global $cfg;
        $menu = _factory('examples_model')->get_menu();
        $rs['menu'] = $menu;
        $rs['root'] = _factory('examples_model')->path;
        $_REQUEST['in'] = $_REQUEST['in'] ? $_REQUEST['in'] : str_replace($rs['root'], '', $menu[0]['content']);
        return array( /*'view'=>'xx/xx/xx', will use default view file:examples/view/examples/index.phtml  */
            'data' => array('rs' => $rs)
        ); //data show in view as $rs
        //THE VIEW DEFAULT:
        // Modules name : "examples"
        // VIEW : "view" - system wording
        // CONTROLLER: "examples"
        // /action method: "index.phtml" - "indexAction" remove Action, add ".phtml"
        // examples/view/examples/index.phtml

    }
}
