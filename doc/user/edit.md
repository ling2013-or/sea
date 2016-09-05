# 修改密码

>接口描述 (通过旧密码修改)

| 接口名称 | *修改密码* |
|----------|--------|
|**接口地址**|/User/edit|
|**请求方式**|POST|
|**数据格式**|<code>JSON</code>|

##请求参数
[<公共传入参数>](../README.md)  

|编码|名称|类型|必须|说明|默认值|
|:---|:---|:---|:--:|:---|:-----|
|old|旧密码|<code>string</code>|是|旧密码|无|
|password|密码|<code>string</code>|是|密码|无|
|password_repeat|重复密码|<code>string</code>|是|重复密码|无|

##返回参数
[<公共返回参数>](../README.md)

##接口示例

>请求示例

```json
	password:xxxx
	password_repeat:xxxx
	old:xxxx
```
>接收成功示例

```json
{code: 0, status: true, msg: "密码修改成功！", data: {mobile: "xxxx"}}
	code: 0
	msg: "密码修改成功！"
	status: true
```