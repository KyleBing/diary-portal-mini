<?php
/**
 * 返回结果集
 * Created by PhpStorm.
 * User: Kyle
 * Date: 2019-03-13
 * Time: 17:02
 */


class Response
{
    var $success;
    var $info;

    public function __construct(string $info)
    {
        $this->info = $info;
    }

    public function toJson(){
        return json_encode($this);
    }


    public function getSuccess()
    {
        return $this->success;
    }

    public function setSuccess($success): void
    {
        $this->success = $success;
    }

    public function getInfo()
    {
        return $this->info;
    }

    public function setInfo($info): void
    {
        $this->info = $info;
    }
}

class ResponseError extends Response
{
    var $success = false;
    var $logined = true;
    var $info   = '';

    public function isLogined(): bool
    {
        return $this->logined;
    }

    public function setLogined(bool $logined): void
    {
        $this->logined = $logined;
    }

    public function __construct(string $info = '请求失败')
    {
        parent::__construct($info);
    }


}

class ResponseSuccess extends Response
{
    var $data;
    var $success = true;
    var $info = '';

    public function __construct(string $info = '请求成功')
    {
        parent::__construct($info);
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data): void
    {
        $this->data = $data;
    }
}

class ResponseLogin extends Response
{
    var $success = true;
    var $info = '';
    var $token = '';
    var $email = '';
    var $uid = '';
    var $username='';

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function __construct(string $info = '登录成功')
    {
        parent::__construct($info);
        logLastLoginTime($_COOKIE['diaryEmail']); // 所有成功都记录最后请求时间
    }
    public function getUid(): string
    {
        return $this->uid;
    }

    public function setUid(string $uid): void
    {
        $this->uid = $uid;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
}

// 记录用户登录时间
function logLastLoginTime($email){
    $con = new dsqli();
    $result = $con->query(MSql::InsertLoginLog($email));
    $con->close();
}

