# 产品/组合列表

>接口描述（ ）

| 接口名称 | *列表* |
|----------|--------|
|**接口地址**|/Goods/lists|
|**请求方式**|POST|
|**数据格式**|<code>JSON</code>|

##请求参数
[<公共传入参数>](../README.md)  

|编码 |名称|类型|说明|默认值|
|:----|:---|:---|:---|:-----|
|goods_type|类型|<code>string</code>|产品类型(产品：goods,组合：package)|请求套餐时需要传递，默认为产品|


##返回参数
[<公共返回参数>](../README.md)

|编码|名称|类型|说明|默认值|
|:---|:---|:---|:--:|:---|
|code|状态码|<code>int</code>|返回状态码，用于调试|无|
|status|状态|<code>bool</code>|返回状态：true-成功，false-失败|无|
|data|返回数据|<code>object</code>|暂无|无|

参数项：data

|编码 |名称|类型|说明|默认值|
|:----|:---|:---|:---|:-----|
|id|产品自增ID|<code>int</code>|产品自增ID|无|
|name|产品名称|<code>string</code>|产品名称|无|
|title|产品简介|<code>string</code>|产品简介|无|
|picture|产品封面图|<code>string</code>|图片链接|无|
|picture_more|产品介绍图|<code>array</code>|详情图片|无|
|sales|产品累计销量|<code>int</code>|总销量|无|
|sales_month|月销量|<code>int</code>|月销量|无|
|browse|浏览量|<code>int</code>|浏览量|无|
|stock|库存|<code>int</code>|库存|无|
|price|实际价格|<code>string</code>|实际价格|无|
|mark_price|市场价格|<code>string</code>|市场价格|无|
|video_id|视频编号|<code>string</code>|视频编号|无|


##接口示例

>请求示例
```


```


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
                "id": "1",
                "name": "海参",
                "title": "123123123123",
                "picture": "/uploads/Picture/goods/2016-07-20/ae5e9c334511b8e728c87abffa515347.jpg",
                "picture_more": [
                    "/uploads/Picture/goods/2016-07-20/a21e136482984686e22c513c83fd7c36.jpg",
                    "/uploads/Picture/goods/2016-07-20/1117d067a715fd98ab18097d98c261aa.jpg",
                    "/uploads/Picture/goods/2016-07-21/9f127f6737db55cf0893b8f521c46cfc.jpg"
                ],
                "sales": "500",
                "real_sales": "-2",
                "browse": "6",
                "stock": "124",
                "price": "100.000",
                "mark_price": "200.000",
                "breeder": null,
                "breeder_picture": null,
                "breeder_profile": null,
                "goods_type": "0",
                "description": "<p>123111</p>",
                "\r\ncomment": "0",
                "status": "1",
                "add_time": "1469004738",
                "gids": null,
                "rate": null,
                "goods_star": "0"
            }
        ]
    }
}

```
