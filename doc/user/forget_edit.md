#忘记密码手机验证

>接口描述

| 接口名称 | *修改密码（忘记密码第二步）* |
|----------|--------|
|**接口地址**|/User/forget_edit|
|**请求方式**|POST|
|**数据格式**|<code>JSON</code>|

##请求参数
[<公共传入参数>](../README.md)  

|编码|名称|类型|必须|说明|默认值|
|:---|:---|:---|:--:|:---|:-----|
|mobile|手机号|<code>string</code>|是|发送的手机号码|无|
|password|新密码|<code>string</code>|是|新密码|无|
|password_repeat|重复密码|<code>string</code>|是|重复密码|无|
|key|钥匙|<code>string</code>|是|在第一步短信验证的时候获取到一个密匙|无|

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
	password:xxx
	password_repeat:xxx
	key:xxx
	mobile:xxx
```


>接收成功示例

```json
{code: 0, status: true, msg: "密码修改成功！", data: {mobile: "18566556666"}}
	code: 0
	data: {mobile: "18566556666"}
			mobile: "18566556666"
	msg: "密码修改成功！"
	status: true
```