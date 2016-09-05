<?php
namespace Api\Controller;

use Common\Library\Curl\Curl;
use Common\Library\JWT\JWT;

class IndexController extends ApiController
{
    public function test()
    {
        $d = D('SellOrder');
        $d->extract(16,16);

    }
    public function index()
    {

        $curl = new Curl();

        $url = 'www.tianyuanshuo.cn/api.php/?c=Member&a=login';

        $data = '{"safejson":"rIegwqjV29CnlFhvV5WWlcTFlVNfV66pmpWHn4djnZdpnGSlZY+ck25kY2l3Z2VhpJVqaWB5fGdmY5eZpmmqpnOHXIOlytPPmqSvpZpWb1aUhmBTl5qvnZSW2dTQldOHa4dglWaVxcdpZ5qbaFZhVtnJpqScpKdWa1OWk5VelYddh6DJpNDJz6qdmJqnVm9WlJxpYWRnbmlpYZqH4g=="}';

        $curl->setHeader('Content-Type','application/json');
        $curl->post($url, $data);
        dump($curl->rawResponse);

        die;

        $key = "example_key";
        $token = array(
            "iss" => "http://example.org",
            "aud" => "http://example.com",
            "iat" => 1356999524,
            "nbf" => time()
        );

        dump($token);

        /**
         * IMPORTANT:
         * You must specify supported algorithms for your application. See
         * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
         * for a list of spec-compliant algorithms.
         */
        $jwt = JWT::encode($token, $key);
dump($jwt);

        $decoded = JWT::decode($jwt, $key, array('HS256'));

        dump($decoded);
    }
}