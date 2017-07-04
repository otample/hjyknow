<?php
/**
 * Created by PhpStorm.
 * User: xingxing
 * Date: 2017/5/2
 * Time: 13:38
 */
$environment = $app->environment();

//function http_request($url, $header=array(), $data = null)
//{
//    $curl = curl_init();
//    curl_setopt($curl, CURLOPT_URL, $url);
//    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
//    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
//    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
//    if (!empty($data)){
//        curl_setopt($curl, CURLOPT_POST, 1);
//        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
//    }
//    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
//    $output = curl_exec($curl);
//    curl_close($curl);
//    return $output;
//}

//$url = 'http://hfhjsrv.ticp.net:8000/tms/CalcFee';
//$data = [
//    "ProductID"=>"ARAMEX",
//    "DestCountry"=>"NL",
//    "Weight"=>"3.940",
//    "VOL"=>"1.2"
//];
//$str  = "";
//    foreach ( $data as $k => $v )
//    {
//        $str .= "$k=" . urlencode( $v ). "&" ;
//    }
//    $post_data = substr($str ,0,-1);
//$post_data = json_encode($post_data);
//echo '<pre>';
//    var_dump($post_data);
//$header = [
//    'Account'=>'test',
//    'Token'=>'test'
//];
//var_dump($header);
//var_dump($url);
//$data = http_request($url,$header,$post_data);

/*
 * 100块砖，100人，男的4块一次，女的2块一次，小孩4人一块
 * 男人:x,最少1人,最多20人
 * 女人:y,z最少1人
 * 小孩:100-(x+y),最少4人
 * */
    for( $x=1;$x<=20;$x++){
        for( $y=1;$y<=(96-$x);$y++){
            if( (4*$x) + (2*$y) + (100-$x-$y)/4 == 100){
                echo '男人:'.$x.'人|| 女人:'.$y.'人|| 小孩:'.(100-$x-$y).'人<br/>';
            }
        }
    }
?>
<html>

    <form action="./1.php" method="post" name="submit_form" onsubmit=" return true">
        <input type='text' name='test' id="fin"/><br/>
        <input type="checkbox" name="a[1]" value="1">1
        <input type="checkbox" name="a[2]" value="2">2
        <input type="checkbox" name="a[3]" value="3">3<br/>
        <select id="sel" name="aaa">
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
        </select>
        <br/>
        <input type="submit" value="点击" onclick="check_sub()" / >
    </form>
    <script>

        var check_sub = function(){
            var test = submit_form.test.value;
//            if (test == '啊') {
            test  = 'asd';
//            alert(test);
//            }
//            1.全文匹配后将汉字提取出,排除标点影响(遇到标点后重新计数)
//            2.将汉字,相邻的两位成一组,匹配字典,
//            3.替换
        }
    </script>

</html>
