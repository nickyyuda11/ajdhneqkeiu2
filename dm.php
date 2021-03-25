<?php
include 'class_ig_new_reply.php';
error_reporting(0);
$u = getUsername();
$p = getPassword();
echo PHP_EOL;
$text_comment = getComment('[?] Enter your file comment here: ');
$getfile = file_get_contents($text_comment);
$x = explode("|", $getfile);
$c = count($x) - 1;
$target = getUsername('[?] Enter ID Target: ');
$login = logins($u, $p);
if ($login['status'] == 'success') {

    echo color()["LG"] . '[*] Login as ' . $login['username'] . ' Success!' . PHP_EOL;
    $data_login = array(
        'username' => $login['username'],
        'csrftoken' => $login['csrftoken'],
        'sessionid' => $login['sessionid'],
        'ds_user_id' => $login['ds_user_id'],
        'ig_did' => $login['ig_did'],
        'mid' => $login['mid'],
        'useragent' => $login['useragent']
    );

    shuffle($x);
    $text = $x[0];
    $process = ProcessDM($target, $data_login);
    echo $process['thread_id'];
    $dm = KirimDM($text, $process['thread_id'], $target, $data_login);

    var_dump($dm);
}
