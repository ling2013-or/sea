##产品/组合评论列表

>接口描述

| 接口名称 | *评论列表* |
|----------|--------|
|**接口地址**|/Goods/commentlist|
|**请求方式**|POST|
|**数据格式**|<code>JSON</code>|

##请求参数
[<公共传入参数>](../README.md)  

|编码|名称|类型|必须|说明|默认值|
|:---|:---|:---|:--:|:---|:-----|
|goods_id|商品/组合ID|<code>int</code>|是|商品/组合ID|无|





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
|user_name|用户名称|<code>string</code>|名称|无|
|user_avatar|用户头像|<code>string</code>|评论者头像|无|
|comment|评论信息|<code>string</code>|用户评论信息|无|
|comment_time|评价时间|<code>string</code>|时间戳|无|


##接口示例

>请求示例



>接收成功示例

```
{
    "code": 0,
    "status": true,
    "msg": "成功",
    "data": {
        "page": 1,
        "count": "1",
        "list": [
            {
                "id": "9",
                "comment": "123123",
                "comment_time": "1453861418",
                "user_name": "userliu",
                "user_avatar": "/uploads/2016-01-26/56a714be8da26.jpg"
            }
        ]
    }
}

```