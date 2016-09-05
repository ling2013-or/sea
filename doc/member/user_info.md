#修改用户基本信息：昵称、真实姓名、邮箱

>接口描述

| 接口名称 | *修改用户基本信息：昵称、真实姓名、邮箱* |
|----------|--------|
|**接口地址**|/Member/user_info|
|**请求方式**|POST|


##请求参数
[<公共传入参数>](../README.md)  

|编码|名称|类型|必须|说明|默认值|
|:---|:---|:---|:--:|:---|:-----|
|farm_name|农场名|<code>string</code>|是|农场名称|无
|user_name|用户名|<code>string</code>|是|用户名|无
|nick_name|昵称|<code>string</code>|是|用户昵称|无
|real_name|真实姓名|<code>string</code>|是|真实姓名|无
|user_email|邮箱|<code>string</code>|是|邮箱地址|无

##返回参数
[<公共返回参数>](../README.md)

|编码|名称|类型|说明|默认值|
|:---|:---|:---|:--:|:---|:-----|
|data|返回数据|<code>json</code>|暂无|无|


##接口示例

>请求示例



>接收成功示例

```json

{
    "code": 0,
    "status": true,
    "msg": "更新用户信息成功"
}

```