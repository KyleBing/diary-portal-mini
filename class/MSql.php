<?php
/**
 * SQL 操作
 * Created by PhpStorm.
 * User: Kyle
 * Date: 2019-03-12
 * Time: 18:00
 */

require "config.php";

date_default_timezone_set('Asia/Shanghai');


class MSql
{

    public static $CREATEDIARIES = "CREATE TABLE `diaries` (
                                      `id` int(11) NOT NULL AUTO_INCREMENT,
                                      `create_date` datetime NOT NULL,
                                      `content` varchar(255) NOT NULL,
                                      `category` enum('life','study','film','game','work','sport','bigevent','other') NOT NULL DEFAULT 'life',
                                      `modify_date` datetime DEFAULT NULL,
                                      `date` datetime NOT NULL,
                                      `uid` int(11) NOT NULL,
                                      PRIMARY KEY (`id`)
                                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
    public static $CREATELOGINLOG = "CREATE TABLE `login_log` (
                                      `id` int(11) NOT NULL AUTO_INCREMENT,
                                      `date` datetime NOT NULL,
                                      `email` varchar(50) NOT NULL,
                                      PRIMARY KEY (`id`)
                                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
    public static $CREATEUSERS = "CREATE TABLE `users` (
                                      `uid` int(11) NOT NULL AUTO_INCREMENT,
                                      `email` varchar(50) NOT NULL,
                                      `password` varchar(100) NOT NULL,
                                      `last_visit_time` datetime DEFAULT NULL,
                                      `username` varchar(50) DEFAULT NULL,
                                      `register_time` datetime DEFAULT NULL,
                                      PRIMARY KEY (`uid`,`email`)
                                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";


    /************************* 日记操作 *************************/

    // 搜索日记
    public static function SearchDiaries($uid, $categories, $keyword, $startPoint, $pageCount)
    {
        $categoryStr = '';
        if (count($categories) > 0) {
            for($i=0; $i<count($categories); $i++){
                $template = " or category='${categories[$i]}'";
                $categoryStr .= $template;
            }
            $categoryStr = substr($categoryStr,4);
        } else {
            $categoryStr = "category = ''";
        }
        $sql = "SELECT *
                  from diaries 
                  where uid='${uid}' 
                  and (${categoryStr})
                  and ( title like '%${keyword}%' or content like '%${keyword}%')
                  order by date desc  
                  limit $startPoint, $pageCount";
        return $sql;
    }

    // 添加日记
    public static function AddDiary($uid, $title, $content, $category, $weather, $temperature, $date)
    {
        $timeNow = date('Y-m-d H:i:s');
        $parsed_title = addslashes($title);
        $parsed_content = addslashes($content);
        return "INSERT into diaries(title,content,category,weather,temperature,create_date,modify_date,date,uid)
                VALUES('${parsed_title}','${parsed_content}','${category}','${weather}','${temperature}','${timeNow}','${timeNow}','${date}','${uid}')";
    }

    // 删除日记
    public static function DeleteDiary($uid, $id)
    {
        return "DELETE from diaries
                WHERE id='${id}'
                and uid='${uid}'";
    }

    // 更新日记
    public static function UpdateDiary($uid, $id, $title, $content, $category, $weather, $temperature, $date)
    {
        $timeNow = date('Y-m-d H:i:s');
        $parsed_title = addslashes($title);
        $parsed_content = addslashes($content);
        $sql =  "update diaries 
                set diaries.modify_date='${timeNow}', 
                  diaries.date='${date}', 
                  diaries.category='${category}',
                  diaries.title='${parsed_title}',
                  diaries.content='${parsed_content}',
                  diaries.weather='${weather}',
                  diaries.temperature='${temperature}'
                WHERE id='${id}' and uid='${uid}'";
        return $sql;
    }

    // 查询日记内容
    public static function QueryDiaries($uid, $id)
    {
        return "select * from diaries
                where uid = '${uid}' and id = '${id}'";
    }


    /************************* 用户操作 *************************/

    //  更新密码
    public static function UpdateUserPassword($email, $password)
    {
        return "update users set `password` = '${password}' where email='${email}'";
    }

    // 查询密码
    public static function QueryUserPassword($email)
    {
        return "select * from users where email='${email}'";
    }

    //  新增用户
    public static function InsetNewUser($email, $password, $username)
    {
        $timeNow = date('Y-m-d H:i:s');
        return "insert into users(email, password, register_time, username) VALUES ('${email}','${password}','${timeNow}','${username}')";
    }

    // 查询用户是否存在
    public static function QueryEmailExitance($email)
    {
        return "select email from users where email='${email}'";
    }

    //  记录用户最后登录时间
    public static function InsertLoginLog($email)
    {
        $timeNow = date('Y-m-d H:i:s');
        return "update users set last_visit_time='${timeNow}' where email='${email}'";
    }

}

/**
 * dsqli 继承 mysqli
 * Created by PhpStorm.
 * User: Kyle
 * Date: 2019-03-12
 * Time: 18:00
 */
class dsqli extends mysqli
{
    public function __construct()
    {
        parent::__construct(HOST, USER, PASSWORD, DATABASE, PORT);
    }

    public function query($query, $resultmode = MYSQLI_STORE_RESULT)
    {
        parent::query("SET NAMES 'utf8'");
        return parent::query($query, $resultmode);
    }
}

