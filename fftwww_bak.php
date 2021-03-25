<?php
include 'class_ig.php';
error_reporting(0);
clear();
echo "
Ξ TITLE  : F-L-C Followers Target (FFT)
Ξ FLPATH : tools/fftauto.php
Ξ CODEBY : Firdy [https://fb.com/6null9]
Ξ FIXBY	 : Andi Muh. Rizqi [ikiganteng]
Ξ UPDATE : Officialputuid [26/01/2020]

";
$u = getUsername();
$p = getPassword();
echo PHP_EOL;
$text_comment = getComment('[?] Enter your file comment here: ');
$getfile = file_get_contents($text_comment);
$x = explode("|", $getfile);
$c = count($x) - 1;
echo '[?] Follow User? (Enter Y or y for YES, Enter any key for NO): ';
$f = trim(fgets(STDIN));
echo '[?] Like Media? (Enter Y or y for YES, Enter any key for NO): ';
$l = trim(fgets(STDIN));
echo '[?] Comment Media? (Enter Y or y for YES, Enter any key for NO): ';
$c = trim(fgets(STDIN));
echo '[?] Unfollow? (Enter Y or y for YES, Enter any key for NO): ';
$uf = trim(fgets(STDIN));
$username_target = getUsername('[?] Enter Username Target: ');
echo '[?] Per-Execution?: ';
$req = trim(fgets(STDIN));
echo '[?] Delay Per-Execution: ';
$sleep = trim(fgets(STDIN));
echo '[?] IP Proxy:Port? (Enter Proxy for used, Enter any key for NO): ';
$proxy = trim(fgets(STDIN));
echo '[?] Proxy User:Pass? (Enter User:Pass, Enter any key for NO): ';
$proxyauth = trim(fgets(STDIN));

echo '•••••••••••••••••••••••••••••••••••••••••' . PHP_EOL . PHP_EOL;
$login = ($proxy != '' && $proxyauth != '') ? login($u, $p, $proxy, $proxyauth) : login($u, $p);
if ($login['status'] == 'success') {

    echo color()["LG"] . '[*] Login as ' . $login['username'] . ' Success!' . PHP_EOL;
    $data_login = array(
        'username' => $login['username'],
        'csrftoken'    => $login['csrftoken'],
        'sessionid'    => $login['sessionid']
    );

    $data_target = ($proxy != '' && $proxyauth != '') ? findProfile($username_target, $data_login, $proxy, $proxyauth) : findProfile($username_target, $data_login);

    if ($data_target['status'] == 'success') {

        echo color()['LC'] . '[*] Target: ' . $data_target['username'] . ' | Name: ' . $data_target['fullname'] . '  | Followers: ' . $data_target['followers'] . ' | Following: ' . $data_target['following'] . ' | Post: ' . $data_target['post'] . ' [*] ' . PHP_EOL . PHP_EOL;

        $cmt = 0;
        for ($i = 1; $i < $data_target['followers']; $i++) {

            $profile = ($proxy != '' && $proxyauth != '') ? getFollowers($username_target, $data_login, $req, $req, $proxy, $proxyauth) : getFollowers($username_target, $data_login, $req, $req);

            foreach ($profile as $rs) {

                $id_user = $rs->id;
                $username = $rs->username;
                echo color()["LG"] . '[+] Username: ' . $username . ' | ';

                $post = ($proxy != '' && $proxyauth != '') ? getPost($username, $data_login, $proxy, $proxyauth) : getPost($username, $data_login);
                if ($post['status'] == 'error') {
                    echo color()["LR"] . 'Error: ' . ucfirst($post['details']) . ' | ';
                    $data_follow = ($f == 'y' or 'y' && $proxy != '' && $proxyauth != '') ? follow($username, $data_login, $proxy, $proxyauth) ?: ($f == 'y' or 'y' && $proxy == '' && $proxyauth == '') ?: follow($username, $data_login) : '';
                    if ($data_follow['status'] == 'success') {

                        echo color()["LG"] . "Follow Success | " . PHP_EOL;
                        $sleep = $sleep;
                    } else {

                        echo color()["LR"] . "Follow Failed: " . ucfirst($data_follow['details']) . " | " . PHP_EOL;
                        sleep(1);
                    }
                } else {
                    $sleep = $sleep;
                    $id_post = $post['id'];

                    if ($f == '') {
                        print("No Follow | ");
                    } else {
                        $data_follow = ($proxy != '' && $proxyauth != '') ? follow($username, $data_login, $proxy, $proxyauth) : follow($username, $data_login);
                        $f = ($data_follow['status'] == 'success') ? print("Follow Success | ") : print("Follow Failed | ");
                    }
                    if ($l == '') {
                        print("No Like | ");
                    } else {
                        $data_like = ($proxy != '' && $proxyauth != '') ? like($id_post, $data_login, $proxy, $proxyauth) : like($id_post, $data_login);
                        $l = ($data_like['status'] == 'success') ? print("Like Success | ") : print("Like Failed | ");
                    }
                    if ($c == '') {
                        print("No Comment | ");
                    } else {
                        shuffle($x);
                        $text = $x[0];
                        $data_comment = ($proxy != '' && $proxyauth != '') ? comment($id_post, $data_login, $text, $proxy, $proxyauth) : comment($id_post, $data_login, $text);
                        $c = ($data_comment['status'] == 'success') ? print("Comment Success: $text | ") : print("Comment Failed: " . ucfirst($data_comment['details']) . " | ");
                    }
                    if ($uf == '') {
                        print("No Unfollow | " . PHP_EOL);
                    } else {
                        $data_unfollow = ($data_comment['status'] == 'error' && $proxy != '' && $proxyauth != '') ? unfollow($username, $data_login, $proxy, $proxyauth) ?: ($data_comment['status'] == 'error' && $proxy == '' && $proxyauth == '') ?: unfollow($username, $data_login) : '';
                        $uf = ($data_unfollow['status'] == 'success') ? print("Unfollow Success | " . PHP_EOL) : print("Unfollow Failed | " . PHP_EOL);
                    }
                }
                sleep($sleep);
            }
        }
    } else {

        echo color()['LR'] . 'Error: ' . ucfirst($data_target['details']);
    }
}
