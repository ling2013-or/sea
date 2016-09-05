#概述

##介绍

- [介绍](#introduction)
- [字符编码](#encode)
- [通讯协议](#base_protocol)
- [接口字段类型约定](#fieldtype)
- [接口格式](#apiformat)

<a name="introduction"></a>
## 1. 介绍

本文档细致描述了“农场”项目中后台与前台通讯的接口内容,为之后的编码提供依据

<a name="encode"></a>
## 2. 字符编码

在当前接口协议中使用的所有中文字符集一律采用 **UTF-8** 编码方式。

<a name="base_protocol"></a>
## 3. 通讯协议

在当前接口一律采用 **HTTP** 协议通讯,并以 **get** 或 **post** 方式提交数据。返回数据为 **json** 格式。


<a name="fieldtype"></a>
## 4. 接口字段类型约定

|类型|说明|备注|
|----|---|----|
|<code>string</code>|字符串|如：test|
|<code>int</code>|整数|如：123|
|<code>float</code>|浮点数|如：100.12|
|<code>bool</code>|布尔值|如：true/false|
|<code>object</code>|对象|如：{"code":"编码"}|
|<code>array</code>|数组|如：[ {"code":"编码1"},{"code":"编码2"} ]|

<a name="apiformat"></a>
## 4. 接口格式


>所有接口的返回报文结构具有固定格式，具体如下所示  

返回 **json** 数据时：
```
{
  "code": "接口状态码",
  "msg": "接口提示信息",
  "data": {  }, // 可有可无
}
```


>每个接口需上传公共参数，<font color=red>公共传入参数</font> 如下所示：
>其他待定

|编码|名称|类型|必须|说明|默认|
|:---|:---|:---|:--:|:---|:-----|
|token|访问令牌|<code>string</code>|否|登录接口除外|无|


>每个接口将返回公共参数，<font color=red>公共返回参数</font>如下所示：

|编码|名称|类型|必须|说明|默认|
|:---|:---|:---|:--:|:---|:-----|
|code|状态编码|<code>int</code>|是|0为正常，具体见《[接口状态码对照表](public/code.md)》|无|
|status|状态|<code>bool</code>|是|true-操作成功，false-操作错误|无|
|msg|状态信息|<code>string</code>|是|暂无|无|
|data|返回数据|<code>object</code>|否|暂无|无|

>部分接口需上传翻页参数，<font color=red>公共翻页参数</font> 如下所示：

|编码|名称|类型|必须|说明|默认值|
|:---|:---|:---|:--:|:---|:-----|
|page|页码|<code>int</code>|否|页码从1开始|1|
|page_size|每页大小|<code>int</code>|否|暂无|10|