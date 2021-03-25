<?php
include 'class_ig_new_reply.php';
error_reporting(0);
clear();
echo "
Ξ TITLE  : Reply First Comment (RFC)
Ξ FLPATH : tools/fftauto.php
Ξ CODEBY : Firdy [https://fb.com/6null9]
Ξ FIXBY	 : Andi Muh. Rizqi [ikiganteng]
Ξ UPDATE : Officialputuid [4/02/2021]

";
$u = getUsername();
$p = getPassword();
echo PHP_EOL;
$text_comment = getComment('[?] Enter your file comment here: ');
$getfile = file_get_contents($text_comment);
$x = explode("|", $getfile);
$c = count($x);
$username_targets = getUsername('[?] Enter Username Target: ');
$targets = explode(",", $username_targets);
$targets_c = count($targets) - 1;
echo '[?] Per-Execution?: ';
$req = trim(fgets(STDIN));
echo '[?] Delay Per-Execution: ';
$sleep = trim(fgets(STDIN));
echo '[?] IP Proxy? (Enter Y or y for YES, Enter any key for NO): ';
$proxy = trim(fgets(STDIN));
echo '[?] Proxy Username & Password, EX: xxxxx:aaaaaaa?: ';
$proxyauth = trim(fgets(STDIN));

echo '•••••••••••••••••••••••••••••••••••••••••' . PHP_EOL . PHP_EOL;
$login = ($proxy != '' && $proxyauth != '') ? logins($u, $p, $proxy, $proxyauth) : logins($u, $p);

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

    // $data_login = array(
    //     'username' => 'summonerswar.hacks',
    //     'csrftoken' => 'vPYa41KYb32WOECvXxOXW39PJzdLeKwt',
    //     'sessionid' => '44831725761:eRsY14kkDQGvA7:22',
    //     'ds_user_id' => '44831725761',
    //     'ig_did' => '8F08DF52-98E1-4EC3-9843-F3E8D680A43C',
    //     'mid' => 'YByTrAAEAAHNHqxaZJOgEX3fdJVa',
    //     'useragent' => 'Instagram 138.0.0.26.117 Android (11/1.4.3; 160; 768x1024; samsung; GT-I9220; GT-I9220; smdkc210; en_US)'
    // );

    echo color()['LC'] . '[*] Target: ' . $username_targets . PHP_EOL . PHP_EOL;
    $save = fopen("target_$u.txt", "w+");
    $saves = fopen("save_target_$u.txt", "w+");
    $getfile = file_get_contents("target_$u.txt");
    $targets_id = explode("|", $getfile);
    while (true) {
        for ($i = 0; $i <= $targets_c; $i++) {
            $data_target = ($proxy != '' && $proxyauth != '') ? getCommentFirst($targets[$i], $data_login, $proxy, $proxyauth) : getCommentFirst($targets[$i], $data_login);
            if ($data_target['status'] == 'success') {
                echo color()['LC'] . '[*] Target: ' . $data_target['username'] . ' | Name: ' . $data_target['fullname'] . '  | Followers: ' . $data_target['followers'] . ' | Following: ' . $data_target['following'] . ' | Post: ' . $data_target['post'] . ' [*] ' . PHP_EOL . PHP_EOL;
                if ($targets_id[$i] != $data_target['shortcode']) {
                    fwrite($save, '' . $data_target['shortcode'] . '' . "|");
                }
            } else {
                echo color()['LC'] . '[*] Target: ' . $data_target['username'] . ' Username tidak ditemukan/privat';
            }
        }

        fclose($save);
        $getfile = file_get_contents("target_$u.txt");
        $targets_id = explode("|", $getfile);
        $targets_c = count($targets_id) - 1;
        for ($i = 0; $i < $targets_c; $i++) {
            $data_target = ($proxy != '' && $proxyauth != '') ? getCommentFirst($targets_id[$i], $data_login, $proxy, $proxyauth) : getCommentFirst($targets_id[$i], $data_login);
            shuffle($x);
            $text = $x[0];
            $comment = ($proxy != '' && $proxyauth != '') ? ReplyComment($data_target['id'], $data_target['commentid'], $data_login, $text, $proxy, $proxyauth) : ReplyComment($data_target['id'], $data_target['commentid'], $data_login, $text);
            if ($comment['status'] == 'success') {
                fwrite($saves, '' . $targets_id[$i] . '' . "|");
                echo color()["LG"] . "Comment Success: " . color()['MG'] . $comment['text'] . color()['CY'] . " " . PHP_EOL;
            } else {
                echo color()["LR"] . "Comment Failed: " . color()['MG'] . $comment['text'] . color()['CY'] . " " . PHP_EOL;
            }
            sleep($sleep);
        }
        echo "Bypass Limit";
        sleep($sleep + 120);
    }
} else {
    echo 'Gagal Login';
    die;
}
