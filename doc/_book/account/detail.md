#账户资金详情

>接口描述（用户必须登录）

| 接口名称 | *登录* |
|----------|--------|
|**接口地址**|/Account/detail|
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
|data|返回数据|<code>json</code>|暂无|无|

参数项：data

|编码 |名称|类型|说明|默认值|
|:----|:---|:---|:---|:-----|
|account_balance|账户余额|<code>float</code>|账户余额|暂无|
|account_amount|账户总额|<code>float</code>|账户总额|暂无|
|investment_amount|累计投入|<code>float</code>|充值+在线购买+系统奖励|暂无|
|consume_amount|累计消费|<code>float</code>|累计消费|暂无|
|charge_amount|累计充值|<code>float</code>|累计充值|暂无|
|freeze_amount|冻结金额|<code>float</code>|提现冻结+其他冻结|暂无|

##接口示例

>请求示例



>接收成功示例
