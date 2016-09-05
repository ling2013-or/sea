#添加收货地址

>接口描述（用户必须登录）

| 接口名称 | 添加收货地址|
|----------|--------|
|**接口地址**|/Address/add|
|**请求方式**|POST|
|**数据格式**|<code>JSON</code>|

##请求参数
[<公共传入参数>](../README.md)  

|编码|名称|类型|必须|说明|默认|
|:---|:---|:---|:--|:---|:-----|
|consignee|收货人|<code>string</code>|是|收货人姓名|无|
|province_id|省（直辖市）ID|<code>int</code>|是|暂无|无|
|city_id|城市ID|<code>int</code>|是|暂无|无|
|area_id|县区主键ID|<code>int</code>|是|暂无|无|
|area_info|区域详情|<code>string</code>|是|省-市-区 拼接|无|
|address|详细地址|<code>string</code>|是|详细地址|无|
|phone|手机号码|<code>string</code>|收货人手机号码|暂无|无|
|zip_code|邮编|<code>int</code>|否|邮编|无|
|is_default|是否为默认地址|<code>int</code>|否|是否默认：0-否，1-默认|0|

##返回参数
[<公共返回参数>](../README.md)

|编码|名称|类型|说明|默认值|
|:---|:---|:---|:--|:---|:-----|
|code|状态码|<code>int</code>|返回状态码，用于调试|无|
|status|状态|<code>bool</code>|返回状态：true-成功，false-失败|无|


##接口示例

>请求示例



>接收成功示例
