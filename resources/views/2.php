<?php
/**
 * Created by PhpStorm.
 * User: xingxing
 * Date: 2017/5/2
 * Time: 14:37
 */

$url = "http://hfhjsrv.ticp.net:8000/tms/CalcFee";
$data['ProductID'] = "ARAMEX";
$data['DestCountry'] = "NL";
$data['Weight'] = "3.940";
$data['VOL'] = "1.2";
$acc = 'test';
$header = array('Account:'.$acc,'Token:test');
//var_dump($header);die;
// $o = "";
// foreach ( $data as $k => $v )
// {
//         $o.= "$k=" .  $v . "&" ;
// }
// $post_data = substr($o,0,-1);
$post_data = json_encode($data);
//var_dump($post_data);die;
$res = http_request($url, $header, $post_data);
echo $res;

function http_request($url, $header=array(), $data = null)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    if (!empty($data)){
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}


