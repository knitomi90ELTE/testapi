<?php
/**
 * Created by PhpStorm.
 * User: Norbert
 * Date: 2016. 06. 20.
 * Time: 21:02
 */

namespace App;

abstract class RestApi {

    protected $methods = [
        'POST' => 'post',
        'GET' => 'get',
        'PUT' => 'put',
        'DELETE' => 'delete'
    ];

    protected $method = null;

    protected $actionName = 'action';

    protected $action = null;
    protected $debug = false;

    protected $argsGet = [];
    protected $argsPost = [];
    protected $argsPut = [];
    protected $argsDelete = [];

    public function __construct() {
        $this->method = strtoupper($_SERVER['REQUEST_METHOD']);
        try {
            $this->buildParams();
            $this->callFunction();
        } catch (Exception $e) {
            $this->onError(null, $e->getMessage(), $e->getCode());
        }
    }

    private function buildParams() {
        switch ($this->method) {
            case 'GET':
                if (!empty($_GET)) {
                    $this->argsGet = $_GET;
                    $this->getAction($this->argsGet);
                    $this->debug($this->argsGet);
                }
                break;
            case 'POST':
                if (!empty($_GET)) {
                    $this->argsGet = $_GET;
                }

                if (!empty($_POST)) {
                    $this->argsPost = $_POST;
                }
                $this->getAction(array_merge_recursive($this->argsGet, $this->argsPost));
                $this->debug(array_merge_recursive($this->argsGet, $this->argsPost));
                break;
            case 'PUT':
                if (!empty($_GET)) {
                    $this->argsGet = $_GET;
                }
                parse_str(file_get_contents('php://input'), $this->argsPut);
                $this->getAction(array_merge_recursive($this->argsGet, $this->argsPut));
                $this->debug(array_merge_recursive($this->argsGet, $this->argsPut));
                break;
            case 'DELETE':
                if (!empty($_GET)) {
                    $this->argsGet = $_GET;
                }
                parse_str(file_get_contents('php://input'), $this->argsDelete);
                $this->getAction(array_merge_recursive($this->argsGet, $this->argsDelete));
                $this->debug(array_merge_recursive($this->argsGet, $this->argsDelete));
                break;
            default:
                throw new Exception('No isset method', 1);
                break;
        }
    }

    private function callFunction() {
        $function = $this->method . ucfirst($this->action);
        if (method_exists($this, $function)) {
            call_user_func(array($this, $function));
        } else {
            throw new Exception('No isset method', 3);
        }
    }

    private function debug($args) {
        if (array_key_exists('_debug', $args)) {
            $this->debug = true;
        }
    }

    private function getAction($args) {
        if (!array_key_exists($this->actionName, $args)) {
            throw new Exception('No action param', 2);
        } else {
            $this->action = $args[$this->actionName];
        }
    }

    public function onSuccess(array $response = array(), $message = null) {
        $result = [
            'success' => true,
            'message' => $message,
            'data' => $response
        ];
        if ($this->debug) {
            $result['args'] = [
                'method' => $this->method,
                'actionName' => $this->actionName,
                'action' => $this->action,
                'GET' => $this->argsGet,
                'POST' => $this->argsPost,
                'PUT' => $this->argsPut,
                'DELETE' => $this->argsDelete,
            ];
        }
        $this->response($result);
    }

    public function onError(array $response = array(), $message = null, $errorCode = 0) {
        $result = [
            'success' => false,
            'message' => $message,
            'data' => $response,
            'errorCode' => $errorCode
        ];
        if ($this->debug) {
            $result['args'] = [
                'method' => $this->method,
                'actionName' => $this->actionName,
                'action' => $this->action,
                'GET' => $this->argsGet,
                'POST' => $this->argsPost,
                'PUT' => $this->argsPut,
                'DELETE' => $this->argsDelete,
            ];
        }
        $this->response($result);
    }

    public function response(array $response = array()) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit();
    }
}