# 获取用户收货地址列表

>接口描述

| 接口名称 | 收货地址列表 |
|----------|--------|
|**接口地址**|/Address/lists|
|**请求方式**|POST|
|**数据格式**|<code>JSON</code>|

##请求参数
[<公共传入参数>](../README.md)  


##返回参数
[<公共返回参数>](../README.md)

|编码|名称|类型|说明|默认值|
|:---|:---|:---|:--|:---|:-----|
|code|状态码|<code>int</code>|返回状态码，用于调试|无|
|status|状态|<code>bool</code>|返回状态：true-成功，false-失败|无|
|data|返回数据|<code>object</code>|暂无|无|

参数项：data

|编码 |名称|类型|说明|默认值|
|:----|:---|:---|:---|:-----|
|id|会员收获地址ID|<code>int</code>|会员收获地址ID|暂无|
|consignee|收货人|<code>string</code>|收货人姓名|暂无|
|area_info|区域详情|<code>string</code>|区域详情|暂无|
|address|详细地址|<code>string</code>|详细地址|暂无|
|phone|电话|<code>string</code>|收货人的联系电话|暂无|
|zip_code|邮编|<code>int</code>|收货人的邮编|暂无|
|is_default|是否为默认地址|<code>int</code>|0非默认，1默认地址|暂无|

##接口示例

>请求示例

>接收成功示例
