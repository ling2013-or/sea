#登录

>接口描述

| 接口名称 | *登录* |
|----------|--------|
|**接口地址**|/User/login|
|**请求方式**|POST|
|**数据格式**|<code>JSON</code>|

##请求参数
[<公共传入参数>](../README.md)  

|编码|名称|类型|必须|说明|默认值|
|:---|:---|:---|:--:|:---|:-----|
|username|用户名|<code>string</code>|是|用户名/邮箱/手机号|无|
|password|密码|<code>string</code>|是|暂无|无|

##返回参数
[<公共返回参数>](../README.md)

|编码|名称|类型|说明|默认值|
|:---|:---|:---|:--:|:---|:-----|
|data|返回数据|<code>json</code>|暂无|无|

参数项：data

|编码 |名称|类型|说明|默认值|
|:----|:---|:---|:---|:-----|
|user_name|用户名|<code>string</code>|用户名|暂无|
|user_phone|手机号码|<code>string</code>|用户绑定的手机号码|暂无|
|user_email|邮箱|<code>string</code>|用户登录邮箱|暂无|
|nick_name|用户昵称|<code>string</code>|用户昵称|暂无|
|farm_name|农场名称|<code>string</code>|用户农场名称|暂无|

##接口示例

>请求示例

>接收成功示例

