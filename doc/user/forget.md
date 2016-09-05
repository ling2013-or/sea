# 忘记密码

>接口描述

| 接口名称 | *忘记密码手机验证* |
|----------|--------|
|**接口地址**|/User/forget|
|**请求方式**|POST|
|**数据格式**|<code>JSON</code>|

##请求参数
[<公共传入参数>](../README.md)  

|编码|名称|类型|必须|说明|默认值|
|:---|:---|:---|:--:|:---|:-----|
|mobile|手机号|<code>int</code>|是|发送的手机号码|无|
|code|验证码|<code>int</code>|是|手机验证码|无|
|password|密码|<code>string</code>|是|新密码|无|

##返回参数
[<公共返回参数>](../README.md)

|编码|名称|类型|说明|默认值|
|:---|:---|:---|:--:|:---|:-----|
|data|返回数据|<code>json</code>|暂无|无|

参数项：data

|编码 |名称|类型|说明|默认值|
|:----|:---|:---|:---|:-----|
|mobile|手机号|<code>int</code>|验证的手机号码|暂无|


##接口示例

>请求示例
```json
	mobile:xxx
	code:xxx
```


>接收成功示例

```json
{code: 0, status: true, msg: "校验成功！", data: {mobile: "xxx"}}
	code: 0
	data: {mobile: "xxx"}
		mobile: "xxx"
	msg: "校验成功！"
	status: true
```