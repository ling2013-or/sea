#快速注册

>接口描述

| 接口名称 | *快速注册* |
|----------|--------|
|**接口地址**|/User/mobileReg|
|**请求方式**|POST|
|**数据格式**|<code>JSON</code>|

##请求参数
[<公共传入参数>](../README.md)  

|编码|名称|类型|必须|说明|默认值|
|:---|:---|:---|:--:|:---|:-----|
|mobile|手机|<code>string</code>|否|手机号码|无|
|password|密码|<code>int</code>|否|登录密码|无|
|password_repeat|密码|<code>string</code>|否|重复密码|无|
|code|验证码|<code>int</code>|否|短信验证码|无|

##返回参数
[<公共返回参数>](../README.md)

|编码|名称|类型|说明|默认值|
|:---|:---|:---|:--:|:---|:-----|
|data|返回数据|<code>json</code>|暂无|无|

参数项：data

|编码 |名称|类型|说明|默认值|
|:----|:---|:---|:---|:-----|
|token|访问令牌|<code>string</code>|访问令牌|暂无|
|mobile|手机号码|<code>int</code>|手机号码|暂无|

##接口示例

>请求示例

```json
	mobile:xxx
	password:xxx
	code:xxx
```

>接收成功示例

```json
{code: 0, status: true, msg: "注册成功！", data: {mobile: "xxx"}}
	code: 0
	data: {mobile: "xxx"}
		mobile: "xxx"
	msg: "注册成功！"
	status: true
```