<?php

$con = mysql_connect("localhost","root","123");
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }

mysql_select_db("edusoho-7.0", $con);
mysql_query("SET NAMES 'UTF8'");

mysql_query("BEGIN");
$result = mysql_query("SELECT * FROM pre_imi_members_bak limit 0,50000" );
$count = 0;
while($row = mysql_fetch_array($result))
  {
    $id =$row['iuid'];
    $nickname =$row['iloginname'];
    $password =$row['ipassword'];
    $salt =$row['isalt'];
    $email =$row['iemail'];
    $createdIp =$row['iregip'];
    $roles =  '|ROLE_USER|';
    $type = 'default';
    $emailVerified = $row['iemailstatus'];
    $createdTime =$row['iregtime'];
    $isExist = mysql_query("SELECT id FROM user where nickname = '".$nickname."' or email = '".$email."'  limit 1" );
    $isExist = mysql_fetch_array($isExist);
    if($isExist) {
        $count ++;
        continue;
    }
    
  mysql_query("INSERT INTO user (id, nickname, password,salt,email,createdIp,roles,type,emailVerified,createdTime) 
VALUES ('".$id."', '".$nickname."', '".$password."', '".$salt."', '".$email."', '".$createdIp."', '".$roles."', '".$type."','".$emailVerified."' ,'".$createdTime."' )");

 if($row['igender'] == 1) {

    $sex = "male";
 }else {
    $sex = "female";
 }
    mysql_query("INSERT INTO user_profile (id, truename, gender) 
VALUES ('".$id."', '".$row['irealname']."', '".$sex."')");

  }
mysql_query("COMMIT");
echo "已导入5００００条\n";

mysql_query("BEGIN");
$result = mysql_query("SELECT * FROM pre_imi_members_bak limit 50000,50000" );

while($row = mysql_fetch_array($result))
  {
    $id =$row['iuid'];
    $nickname =$row['iloginname'];
    $password =$row['ipassword'];
    $salt =$row['isalt'];
    $email =$row['iemail'];
    $createdIp =$row['iregip'];
    $roles =  '|ROLE_USER|';
    $type = 'default';
    $emailVerified = $row['iemailstatus'];
    $createdTime =$row['iregtime'];
    $isExist = mysql_query("SELECT id FROM user where nickname = '".$nickname."' or email = '".$email."'  limit 1" );
    $isExist = mysql_fetch_array($isExist);
    if($isExist) {
         $count ++;
        continue;
    }
    
  mysql_query("INSERT INTO user (id, nickname, password,salt,email,createdIp,roles,type,emailVerified,createdTime) 
VALUES ('".$id."', '".$nickname."', '".$password."', '".$salt."', '".$email."', '".$createdIp."', '".$roles."', '".$type."','".$emailVerified."' ,'".$createdTime."' )");

 if($row['igender'] == 1) {

    $sex = "male";
 }else {
    $sex = "female";
 }
    mysql_query("INSERT INTO user_profile (id, truename, gender) 
VALUES ('".$id."', '".$row['irealname']."', '".$sex."')");

  }
echo "已导入5００００条\n";
mysql_query("COMMIT");
mysql_query("BEGIN");
$result = mysql_query("SELECT * FROM pre_imi_members_bak limit 100000,50000" );

while($row = mysql_fetch_array($result))
  {
    $id =$row['iuid'];
    $nickname =$row['iloginname'];
    $password =$row['ipassword'];
    $salt =$row['isalt'];
    $email =$row['iemail'];
    $createdIp =$row['iregip'];
    $roles =  '|ROLE_USER|';
    $type = 'default';
    $emailVerified = $row['iemailstatus'];
    $createdTime =$row['iregtime'];
    $isExist = mysql_query("SELECT id FROM user where nickname = '".$nickname."' or email = '".$email."'  limit 1" );
    $isExist = mysql_fetch_array($isExist);
    if($isExist) {
         $count ++;
        continue;
    }
    
  mysql_query("INSERT INTO user (id, nickname, password,salt,email,createdIp,roles,type,emailVerified,createdTime) 
VALUES ('".$id."', '".$nickname."', '".$password."', '".$salt."', '".$email."', '".$createdIp."', '".$roles."', '".$type."','".$emailVerified."' ,'".$createdTime."' )");

 if($row['igender'] == 1) {

    $sex = "male";
 }else {
    $sex = "female";
 }
    mysql_query("INSERT INTO user_profile (id, truename, gender) 
VALUES ('".$id."', '".$row['irealname']."', '".$sex."')");

  }
  mysql_query("COMMIT");
echo "已导入5００００条\n";
mysql_query("BEGIN");
$result = mysql_query("SELECT * FROM pre_imi_members_bak limit 150000,50000" );

while($row = mysql_fetch_array($result))
  {
    $id =$row['iuid'];
    $nickname =$row['iloginname'];
    $password =$row['ipassword'];
    $salt =$row['isalt'];
    $email =$row['iemail'];
    $createdIp =$row['iregip'];
    $roles =  '|ROLE_USER|';
    $type = 'default';
    $emailVerified = $row['iemailstatus'];
    $createdTime =$row['iregtime'];
    $isExist = mysql_query("SELECT id FROM user where nickname = '".$nickname."' or email = '".$email."'  limit 1" );
    $isExist = mysql_fetch_array($isExist);
    if($isExist) {
         $count ++;
        continue;
    }
    
  mysql_query("INSERT INTO user (id, nickname, password,salt,email,createdIp,roles,type,emailVerified,createdTime) 
VALUES ('".$id."', '".$nickname."', '".$password."', '".$salt."', '".$email."', '".$createdIp."', '".$roles."', '".$type."','".$emailVerified."' ,'".$createdTime."' )");

 if($row['igender'] == 1) {

    $sex = "male";
 }else {
    $sex = "female";
 }
    mysql_query("INSERT INTO user_profile (id, truename, gender) 
VALUES ('".$id."', '".$row['irealname']."', '".$sex."')");

  }mysql_query("COMMIT");
echo "已导入5００００条\n";
mysql_query("BEGIN");
$result = mysql_query("SELECT * FROM pre_imi_members_bak limit 200000,50000" );

while($row = mysql_fetch_array($result))
  {
    $id =$row['iuid'];
    $nickname =$row['iloginname'];
    $password =$row['ipassword'];
    $salt =$row['isalt'];
    $email =$row['iemail'];
    $createdIp =$row['iregip'];
    $roles =  '|ROLE_USER|';
    $type = 'default';
    $emailVerified = $row['iemailstatus'];
    $createdTime =$row['iregtime'];
    $isExist = mysql_query("SELECT id FROM user where nickname = '".$nickname."' or email = '".$email."'  limit 1" );
    $isExist = mysql_fetch_array($isExist);
    if($isExist) {
         $count ++;
        continue;
    }
    
  mysql_query("INSERT INTO user (id, nickname, password,salt,email,createdIp,roles,type,emailVerified,createdTime) 
VALUES ('".$id."', '".$nickname."', '".$password."', '".$salt."', '".$email."', '".$createdIp."', '".$roles."', '".$type."','".$emailVerified."' ,'".$createdTime."' )");

 if($row['igender'] == 1) {

    $sex = "male";
 }else {
    $sex = "female";
 }
    mysql_query("INSERT INTO user_profile (id, truename, gender) 
VALUES ('".$id."', '".$row['irealname']."', '".$sex."')");

  }mysql_query("COMMIT");
echo "已导入5００００条\n";
mysql_query("BEGIN");
$result = mysql_query("SELECT * FROM pre_imi_members_bak limit 250000,150000" );

while($row = mysql_fetch_array($result))
  {
    $id =$row['iuid'];
    $nickname =$row['iloginname'];
    $password =$row['ipassword'];
    $salt =$row['isalt'];
    $email =$row['iemail'];
    $createdIp =$row['iregip'];
    $roles =  '|ROLE_USER|';
    $type = 'default';
    $emailVerified = $row['iemailstatus'];
    $createdTime =$row['iregtime'];
    $isExist = mysql_query("SELECT id FROM user where nickname = '".$nickname."' or email = '".$email."'  limit 1" );
    $isExist = mysql_fetch_array($isExist);
    if($isExist) {
         $count ++;
        continue;
    }
    
  mysql_query("INSERT INTO user (id, nickname, password,salt,email,createdIp,roles,type,emailVerified,createdTime) 
VALUES ('".$id."', '".$nickname."', '".$password."', '".$salt."', '".$email."', '".$createdIp."', '".$roles."', '".$type."','".$emailVerified."' ,'".$createdTime."' )");

 if($row['igender'] == 1) {

    $sex = "male";
 }else {
    $sex = "female";
 }
    mysql_query("INSERT INTO user_profile (id, truename, gender) 
VALUES ('".$id."', '".$row['irealname']."', '".$sex."')");

  }mysql_query("COMMIT");
echo "已导入5００００条\n";
echo "导入结束\n";
echo $count."人已存在\n";
mysql_close($con);