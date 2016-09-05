#账户资金变动记录

>接口描述（用户必须登录）

| 接口名称 | *登录* |
|----------|--------|
|**接口地址**|/Account/changelist|
|**请求方式**|POST|
|**数据格式**|<code>JSON</code>|

##请求参数
[<公共传入参数>](../README.md)  

|编码|名称|类型|必须|说明|默认值|
|:---|:---|:---|:--:|:---|:-----|
|type|变动类型|<code>string</code>|否|资金变动类型查询条件|无|
|page|查询页码|<code>int</code>|否|分页查询页码数|1|
|page_size|每页显示数|<code>int</code>|否|每次查询分页数量|10|

参数项：type (支持类型)

|编码 |名称|类型|
|:----|:---|:---|
|order_pay|下单支付预存款|<code>string</code>|
|order_freeze|下单冻结预存款|<code>string</code>|
|order_cancel|取消订单解冻预存款|<code>string</code>|
|order_comb_pay|下单支付被冻结的预存款|<code>string</code>|
|recharge|充值|<code>string</code>|
|cash_apply|申请提现冻结预存款|<code>string</code>|
|cash_pay|提现成功|<code>string</code>|
|cash_del|取消提现申请，解冻预存款|<code>string</code>|
|refund|退款|<code>string</code>|

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
|type|变动类型|<code>string</code>|资金变动类型|无|
|affect_money|影响金额|<code>float</code>|正值表示增加，负值表示减少|无|
|available_money|账户余额|<code>float</code>|账户余额|无|
|freeze_money|冻结金额|<code>float</code>|冻结金额|无|
|description|描述|<code>sting</code>|资金变动描述|无|
|add_time|操作时间|<code>int</code>|资金变动时间,UNIX时间戳|无|

##接口示例

>请求示例



>接收成功示例
