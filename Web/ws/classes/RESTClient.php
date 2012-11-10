<?php
/*
 Thanks George A. Papayiannis, most codes of this file are from
 http://www.sematopia.com/2006/10/how-to-making-a-php-rest-client-to-call-rest-resources/
 */
require_once "HTTP/Request.php";

class RESTClient {

    private $curr_url = "";
    private $user_name = "";
    private $password = "";
    private $content_type = "";
    private $response = "";
    private $responseBody = "";
    private $responseCode = "";
    private $req = null;

    public function __construct($user_name="", $password="", $content_type="") {
        $this->user_name = $user_name;
        $this->password = $password;
        $this->content_type = $content_type;
        return true;
    }

    public function createRequest($url, $method, $arr = null) {
        $this->curr_url = $url;
        $this->req = new HTTP_Request($url);
        if ($this->user_name != "" && $this->password != "") {
           $this->req->setBasicAuth($this->user_name, $this->password);
        }
        if ($this->content_type != "") {
           $this->req->addHeader("Content-Type", $this->content_type);
        }

        switch($method) {
            case "GET":
                $this->req->setMethod(HTTP_REQUEST_METHOD_GET);
                break;
            case "POST":
                $this->req->setMethod(HTTP_REQUEST_METHOD_POST);
                $this->addPostData($arr);
                break;
            case "PUT":
                $this->req->setMethod(HTTP_REQUEST_METHOD_PUT);
                $this->addPostData($arr);
                break;
            case "DELETE":
                $this->req->setMethod(HTTP_REQUEST_METHOD_DELETE);
                break;
        }
    }

    private function addPostData($arr) {
        if ($arr != null) {
            if (gettype($arr) == 'string') {
                $this->req->setBody($arr);
            } else {
                foreach ($arr as $key => $value) {
                    $this->req->addPostData($key, $value);
                }
            }
        }
    }

    public function sendRequest() {
        $this->response = $this->req->sendRequest();

        if (PEAR::isError($this->response)) {
            echo $this->response->getMessage();
            die();
        } else {
            $this->responseCode = $this->req->getResponseCode();
            $this->responseBody = $this->req->getResponseBody();
        }
    }

    public function getResponse() {
        return array($this->responseCode, $this->responseBody);
    }

}

?>
