#仿知乎后台API说明 v-1.0.0
##常规API调用原则
    -所有API分为两个部分
        列:public/part_1/part_2
        part_1为model,part_2为方法名
    -所有API都以数组形式返回
        ['status'=>num,'msg'=>your msg,[$data]]
        当存在$data时说明取得数据,当$data存在但是为空数据时说明,
    -CRUD
        每个model中都会有增删改查,对应的方法命一般为add,my_delete,change,search

##Model
###Question
####add
-权限:已登录
-传参:
    -必填:title(标题)
    -可选:desc(问题描述)
####change
-权限:已登录,且为作者
-传参:
    -必填:qid(问题ID)
    -可选:title(标题),desc(问题描述)
####search
-传参:
    -可选:qid(问题ID,有此参数时只查询该问题,没有默认查询10条问题)
####my_delete
-权限:已登录,且为作者
-传参:
    -必填:qid(问题ID)

