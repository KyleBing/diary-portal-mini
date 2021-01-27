<?php
/**
 * 日记的相关操作方法
 * Created by PhpStorm.
 * User: Kyle
 * Date: 2019-03-13
 * Time: 19:24
 */

require "class/Response.php";
require "class/MSql.php";
require "common.php";

// 传 email 是为了避免，邮件正确，日记id不对的情况

if (checkLogin($_POST['email'], $_POST['token'])) {
    switch ($_POST['type']) {
        case 'query':
            queryDiary($_POST['uid'], $_POST['id']);
            break;
        case 'modify':
            updateDiary($_POST['uid'], $_POST['id'], $_POST['title'], $_POST['content'], $_POST['category'], $_POST['weather'], $_POST['temperature'], $_POST['date']);
            break;
        case 'add':
            addDiary($_POST['uid'], $_POST['title'], $_POST['content'], $_POST['category'], $_POST['weather'], $_POST['temperature'], $_POST['date']);
            break;
        case 'delete':
            deleteDiary($_POST['uid'], $_POST['id']);
            break;
        case 'search':
        case 'list':
            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : '';
            $categories = isset($_POST['categories']) ? $_POST['categories'] : '';
            $categories = explode(',', $categories);
            searchDiary($_POST['uid'], $categories, $keyword, $_POST['pageCount'], $_POST['pageNo']);
            break;
        default:
            $response = new ResponseError('请求参数错误');
            echo $response->toJson();
            break;
    }
} else {
    $response = new ResponseError('密码错误，请重新登录');
    $response->setLogined(false);
    echo $response->toJson();
}



// 搜索，展示日记
function searchDiary($uid, $categories, $keyword, $pageCount, $pageNo)
{
    $startPoint = ($pageNo - 1) * $pageCount;
    $con = new dsqli();
    $con->set_charset('utf8');
    $response = '';
    $result = $con->query(MSql::SearchDiaries($uid, $categories, $keyword, $startPoint, $pageCount));
    if ($result) {
        $response = new ResponseSuccess();
        $diaries = $result->fetch_all(1); // 参数1会把字段名也读取出来
        $response->setData($diaries);
    } else {
        $response = new ResponseError();
    }
    echo $response->toJson();
    $con->close();
}

//查询日记内容
function queryDiary($uid, $id)
{
    $con = new dsqli();
    $con->set_charset('utf8');
    $response = '';
    $result = $con->query(MSql::QueryDiaries($uid, $id));
    if ($result) {
        $response = new ResponseSuccess();
        $diaries = $result->fetch_all(1); // 参数1会把字段名也读取出来
        $response->setData($diaries);
    } else {
        $response = new ResponseError();
    }
    echo $response->toJson();
    $con->close();
}


//修改
function updateDiary($uid, $id, $title, $content, $category, $weather, $temperature, $date)
{
    $con = new dsqli();
    $con->set_charset('utf8');
    $response = '';
    $result = $con->query(MSql::UpdateDiary($uid, $id, $title, $content, $category, $weather, $temperature, $date));
    if ($result) {
        $response = new ResponseSuccess('修改成功');
    } else {
        $response = new ResponseError('修改失败');
    }
    echo $response->toJson();
    $con->close();
}


// 删除
function deleteDiary($uid, $id)
{
    $con = new dsqli();
    $con->set_charset('utf8');
    $response = '';
    $result = $con->query(MSql::DeleteDiary($uid, $id));
    if ($result) {
        $response = new ResponseSuccess('删除成功');
    } else {
        $response = new ResponseError('删除失败');
    }
    echo $response->toJson();
    $con->close();
}


// 添加
function addDiary($uid, $title, $content, $category, $weather, $temperature, $date)
{
    $con = new dsqli();
    $con->set_charset('utf8');
    $response = '';
    $result = $con->query(MSql::AddDiary($uid, $title, $content, $category, $weather, $temperature, $date));
    if ($result) {
        $response = new ResponseSuccess('保存成功');
        $queryResult = $con->query('select * from diaries where id=LAST_INSERT_ID()');
        if ($queryResult){
            $response->setData($queryResult->fetch_all(1));
        }
    } else {
        $response = new ResponseError('保存失败');
    }
    echo $response->toJson();
    $con->close();
}


