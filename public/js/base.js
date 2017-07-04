/**
 * Created by HuJiYang on 2017/6/26.
 */
(function(){
    'use strict';//申明自适应
    angular.module('whohu',['ui.router'])//将某块配置到目标文件中

        .config(function($interpolateProvider,
                         $stateProvider,
                         $urlRouterProvider){
            $interpolateProvider.startSymbol('<[:');//定义angular的左边界符
            $interpolateProvider.endSymbol(':]>');//定义angular的右边界符

            $urlRouterProvider.otherwise('/home');

            $stateProvider
                .state('home',{
                    url:'/home',//设置地址栏url显示
                    templateUrl:'home.tpl',//找到ID为home.tpl的模块并只替换该部分
            })
                .state('login',{
                    url:'/login',
                    templateUrl:'login.tpl',
                })
         })


})();