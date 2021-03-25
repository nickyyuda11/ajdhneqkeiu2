<?php
include 'class_ig_new_reply.php';
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
echo "[0] Scrape Likes Target \n";
echo "[1] Scrape Comment Target \n";
$choice = getUsername('[?] Enter Your Choice: ');
$target = getUsername('[?] Enter Link Media Target: ');
$req = getUsername('[?] How Many You Wanna Scrape it Target: ');
echo PHP_EOL;
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

    echo color()['LC'] . '[*] Target: ' . $target . PHP_EOL . PHP_EOL;
    while (true) {
        $profile = ($choice == 0) ? getMediaLikers($target, $data_login, $req, $req) : getCommentId($target, $data_login, $req, $req);
        foreach ($profile as $rs) {
            $file = fopen("$target.txt", "a+");
            $haystack = explode("\n", file_get_contents("$target.txt"));
            $id_user = ($choice == 0) ? $rs->id : $rs->owner->id;
            if ($file && !in_array($id_user, $haystack)) {
                fwrite($file, "$id_user\n");
                echo "Berhasil Grab UID $id_user \n";
            }
        }
        $haystack = explode("\n", file_get_contents("$target.txt"));
        if (count($haystack) >= $req) {
            die;
            fclose($file);
        }
    }
}
