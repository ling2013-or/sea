# 产品/组合的养殖计划

>接口描述（）

| 接口名称 | *修改* |
|----------|--------|
|**接口地址**|/Goods/goodsPlanList|
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
|:---|:---|:---|:--:|:---|:-----|
|code|状态码|<code>int</code>|返回状态码，用于调试|无|
|status|状态|<code>bool</code>|返回状态：true-成功，false-失败|无|
|data|返回数据|<code>object</code>|暂无|无|

参数项：data

|编码 |名称|类型|说明|默认值|
|:----|:---|:---|:---|:-----|
|start_time|开始养殖时间|<code>int</code>|时间戳|0|
|title|标题|<code>string</code>|养殖标题|0|
|content|养殖内容|<code>string</code>|养殖描述|0|


##接口示例

>请求示例



>接收成功示例

```
{
    "code": 0,
    "status": true,
    "msg": "成功",
    "data": [
        {
            "id": "1",
            "goods_id": "1",
            "start_time": "1",
            "title": "标题",
            "content": "你好呀",
            "pic": null,
            "add_time": "1469062515",
            "status": "0",
            "update_time": "0",
            "num": "0"
        },
       
    ]
}

```