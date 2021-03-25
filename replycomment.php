<?php
include 'class_ig_new_reply.php';
error_reporting(0);
clear();
echo "
Ξ TITLE  : Reply First Comment (RFC)
Ξ FLPATH : tools/replycomment.php
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
echo '[?] Max Execution?: ';
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
    for ($i = 0; $i <= $targets_c; $i++) {
        $data_target = ($proxy != '' && $proxyauth != '') ? getIDUsername($targets[$i], $data_login, $proxy, $proxyauth) : getIDUsername($targets[$i], $data_login);
        $dataid_post = ($proxy != '' && $proxyauth != '') ? getMediaId($data_target['id'], $data_login, 1, 1, $proxy, $proxyauth) : getMediaId($data_target['id'], $data_login, 1, 1);
        foreach ($dataid_post as $id_post) {
            $shortcode = $id_post->shortcode;
            $id_post = $id_post->id;
            var_dump($shortcode);
            var_dump($id_post);
        }
        if ($id_post) {
            echo color()['LC'] . '[*] Target: ' . $data_target['username'] . ' | Post: https://instagram.com/p/' . $shortcode . ' [*] ' . PHP_EOL . PHP_EOL;
            $save = fopen("target/target_$targets[$i]_$u.txt", "w+");
            fwrite($save, '' . $id_post . '' . "|" . '' . $data_target['id'] . '' . "|" . '' . $shortcode . '');
            fclose($save);
        } else {
            echo color()['LC'] . '[*] Target: ' . $data_target['username'] . ' Username tidak ditemukan/privat';
        }
    }
    echo color()['LC'] . '================================' . PHP_EOL . PHP_EOL;
    $max = 0;
    $save = fopen("target/target_$u.txt", "w+");
    fwrite($save, '' . $max . '');
    fclose($save);
    while (true) {
        $max = file_get_contents("target/target_$u.txt");
        if ($max >= $req) {
            $max = 0;
            $save = fopen("target/target_$u.txt", "w+");
            fwrite($save, '' . $max . '');
            fclose($save);
            $sleep = sleep(3600 * 12);
            echo "[+] Bypass Limit $sleep Detik" . PHP_EOL . PHP_EOL;
        }
        $targets_c = count($targets) - 1;
        for ($i = 0; $i <= $targets_c; $i++) {
            $getfile = file_get_contents("target/target_$targets[$i]_$u.txt");
            $targets_id = explode("|", $getfile);
            $data_target = ($proxy != '' && $proxyauth != '') ? getIDUsername($targets[$i], $data_login, $proxy, $proxyauth) : getIDUsername($targets[$i], $data_login);
            $dataid_post = ($proxy != '' && $proxyauth != '') ? getMediaId($data_target['id'], $data_login, 1, 1, $proxy, $proxyauth) : getMediaId($data_target['id'], $data_login, 1, 1);
            foreach ($dataid_post as $id_post) {
                $shortcode = $id_post->shortcode;
                $id_post = $id_post->id;
                var_dump($shortcode);
                var_dump($id_post);
            }
            echo color()['LC'] . '[*] Target: ' . $targets[$i] . ' | Post: https://instagram.com/p/' . $shortcode . ' [*] ' . PHP_EOL . PHP_EOL;
            if ($id_post == '') {
                echo "[+] Skipped" . PHP_EOL . PHP_EOL;
            } elseif ($targets_id[2] != $shortcode) {
                $data_comment = ($proxy != '' && $proxyauth != '') ? getCommentId($shortcode, $data_login, 1, 1, $proxy, $proxyauth) : getCommentId($shortcode, $data_login, 1, 1);
                foreach ($data_comment as $id_comment) {
                    $username = $id_comment->owner->username;
                    $id_comment = $id_comment->id;
                    var_dump($username);
                    var_dump($id_comment);
                }
                shuffle($x);
                $text = $x[0];
                $comment = ($proxy != '' && $proxyauth != '') ? ReplyComment($id_post, $id_comment, $data_login, $username, $text, $proxy, $proxyauth) : ReplyComment($id_post, $id_comment, $data_login, $username, $text);
                if ($comment['status'] == 'success') {
                    $save = fopen("target/target_$targets[$i]_$u.txt", "w+");
                    fwrite($save, '' . $id_post . '' . "|" . '' . $data_target['id'] . '' . "|" . '' . $shortcode . '');
                    fclose($save);
                    $maxs = $max + 1;
                    $save = fopen("target/target_$u.txt", "w+");
                    fwrite($save, '' . $maxs . '');
                    fclose($save);
                    echo color()["LG"] . "[+] Comment Success: " . color()['MG'] . $comment['text'] . color()['CY'] . " " . PHP_EOL . PHP_EOL;
                } else {
                    echo color()["LR"] . "[+] Comment Failed: " . color()['MG'] . $comment['details'] . color()['CY'] . " " . PHP_EOL . PHP_EOL;
                }
                sleep($sleep);
            } else {
                echo "[+] Skipped" . PHP_EOL . PHP_EOL;
            }
        }
        echo "[+] Bypass Limit" . PHP_EOL . PHP_EOL;
        sleep($sleep + 120);
    }
} else {
    echo 'Gagal Login';
    die;
}
