#账户资金变动记录

>接口描述（用户必须登录）

| 接口名称 | *登录* |
|----------|--------|
|**接口地址**|/Account/chargelist|
|**请求方式**|POST|
|**数据格式**|<code>JSON</code>|

##请求参数
[<公共传入参数>](../README.md)  

|编码|名称|类型|必须|说明|默认值|
|:---|:---|:---|:--:|:---|:-----|
|page|查询页码|<code>int</code>|否|分页查询页码数|1|
|page_size|每页显示数|<code>int</code>|否|每次查询分页数量|10|

##返回参数
[<公共返回参数>](../README.md)

|编码|名称|类型|说明|默认值|
|:---|:---|:---|:--:|:---|:-----|
|code|状态码|<code>int</code>|返回状态码，用于调试|无|
|status|状态|<code>bool</code>|返回状态：true-成功，false-失败|无|
|data|返回数据|<code>object</code>|暂无|无|

参数项：data

|编码 |名称|类型|说明|默认值|
|:----|:---|:---|:---|:-----|
|page|页码|<code>int</code>|当前查询页码数|1|
|count|总条数|<code>int</code>|数据总条数|0|
|list|数据列表|<code>object</code>|数据列表|空字符串|

参数项：list

|编码 |名称|类型|说明|默认值|
|:----|:---|:---|:---|:-----|
|charge_sn|充值编号|<code>string</code>|记录唯一标识|无|
|charge_amount|充值金额|<code>float</code>|充值金额|无|
|payment_name|支付方式名称|<code>float</code>|支付方式名称|空字符串|
|add_time|添加时间|<code>int</code>|UNIX时间戳|无|
|payment_time|支付时间|<code>int</code>|UNIX时间戳|0|
|payment_state|支付状态|<code>int</code>|0-未支付，1-已支付|0|

##接口示例

>请求示例



>接收成功示例
