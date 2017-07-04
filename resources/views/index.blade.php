<!doctype html>
<html lang="cn" ng-app="whohu">
<head>
    <meta charset="UTF-8">
    <title>index</title>
    <link rel="stylesheet" href="./node_modules/normalize-css/normalize.css">
    <script src="./node_modules/jquery/dist/jquery.js"></script>
    <script src="./node_modules/angular/angular.js"></script>
    <script src="./node_modules/angular-ui-router/angular-ui-router.js"></script>
    <script src="./js/base.js"></script>
</head>
<body>
    <div class="navbar">
        <div>
            <a href="" ui-sref="home">首页</a>
            <a href="" ui-sref="login">登录</a>
        </div>
    </div>
    <div>
        <div ui-view style="height:300px;width:300px;border:1px solid red;"></div>
    </div>
<scritp type="text/ng-template" id="home.tpl">
    <h1>首页</h1>
</scritp>
<scritp type="text/ng-template" id="login.tpl">
    <h1>登录</h1>
</scritp>
   <?php
           session_start();
           $u1 = ['id'=>1,'name'=>'user1'];
            $u2 = ['id'=>2,'name'=>'user2'];
   $_SESSION['user'][$u1['id']] = $u1;
   $_SESSION['user'][$u2['id']] = $u2;
           dd($_SESSION['user']);
   ?>
</body>
</html>
