<?php
	$accountid = "1";
	$tk = "KKKKKKKKKK";
	$url = 'http://www.deskgeo.com/page/ping';
	$counting = 100;
        $fields = array(
                'accountid'      => urlencode($accountid),
                'tk'      => urlencode($tk),
                'counter' => urlencode($counting),
                'ddos'    => urlencode('Y'),
                'msg'     => urlencode('PING')
        );
        $postvars = "";
        foreach($fields as $key=>$value){
                $postvars .= $key . "=" . $value . "&";
        }
        $ch = curl_init();
        $proxy = "mentorandroid.intrantet.com.br:8080";
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        print_r("\n- curl response is - ".$response." ----- ");

?>
