<?php
include 'class_ig_new_reply.php';
// error_reporting(0);
clear();
echo "
Ξ TITLE  : F-L-C-DM Media Target (FFT)
Ξ FLPATH : tools/fftauto.php
Ξ CODEBY : Firdy [https://fb.com/6null9]
Ξ FIXBY	 : Andi Muh. Rizqi [ikiganteng]
Ξ UPDATE : Officialputuid [26/01/2020]

";

include 'AUTH.php';

function Run($results)
{
    echo '[?] Random FL, LIKE, COMMENT, DM? (Enter Y or y for YES, Enter any key for NO): ';
    $r = trim(fgets(STDIN));
    if ($r == 'y') {
        $flcdm = 'random';
        $f = 'random';
        $l = 'random';
        $c = 'random';
        $dm = 'random';
    } else {
        echo '[?] Follow User? (Enter Y or y for YES, Enter any key for NO): ';
        $f = trim(fgets(STDIN));
        echo '[?] Like Media? (Enter Y or y for YES, Enter any key for NO): ';
        $l = trim(fgets(STDIN));
        echo '[?] Comment Media? (Enter Y or y for YES, Enter any key for NO): ';
        $c = trim(fgets(STDIN));
        if ($c == 'y' or $c ==  'Y') {
            $text_comment = getComment('[?] Enter your file comment here: ');
            $getfile = file_get_contents($text_comment);
            $x = explode("|", $getfile);
            $c_c = count($x) - 1;
        }
        echo '[?] DM User? (Enter Y or y for YES, Enter any key for NO): ';
        $dm = trim(fgets(STDIN));
        if ($dm == 'y' or $dm == 'Y') {
            $text_dm = getComment('[?] Enter your DM Text here: ');
            $text_dm_g = file_get_contents($text_dm);
            $text_dm_p = explode("|", $text_dm_g);
            $text_dm_c = count($text_dm_p) - 1;
            $link_dm = getComment('[?] Enter your DM Link here: ');
            $getsfile = file_get_contents($link_dm);
            $link = explode("|", $getsfile);
            $link_c = count($link) - 1;
        }
    }
    echo '[?] Unfollow User Comment Error? (Enter Y or y for YES, Enter any key for NO): ';
    $uf = trim(fgets(STDIN));
    $target = getUsername('[?] Enter Link Media Target (Use , for Many Targets): ');
    $target_p = explode(",", $target);
    echo '[?] Per-Execution?: ';
    $req = trim(fgets(STDIN));
    echo '[?] Delay Per-Execution: ';
    $sleep = trim(fgets(STDIN));
    echo '[?] IP Proxy:Port? (Enter Proxy for used, Enter any key for NO): ';
    $proxy = trim(fgets(STDIN));
    echo '[?] Proxy User:Pass? (Enter User:Pass, Enter any key for NO): ';
    $proxyauth = trim(fgets(STDIN));

    echo '•••••••••••••••••••••••••••••••••••••••••' . PHP_EOL . PHP_EOL;
    echo color()["LG"] . '[*] Login as ' . $results['username'] . ' Success!' . PHP_EOL;
    $data_login = array(
        'username' => $results['username'],
        'csrftoken' => $results['csrftoken'],
        'sessionid' => $results['sessionid'],
        'ds_user_id' => $results['ds_user_id'],
        'ig_did' => $results['ig_did'],
        'mid' => $results['mid'],
        'useragent' => $results['useragent']
    );

    echo color()['LC'] . '[*] Target: ' . $target . PHP_EOL . PHP_EOL;

    while (true) {
        $targets = $target_p[rand(0, (count($target_p) - 1))];
        echo color()['LC'] . '[*] Collecting Target: ' . $targets . PHP_EOL . PHP_EOL;
        $profile = ($proxy != '' && $proxyauth != '') ? getMediaLikers($targets, $data_login, $req, $req, $proxy, $proxyauth) : getMediaLikers($targets, $data_login, $req, $req);

        foreach ($profile as $rs) {
            if ($rs->is_private == true) {
            } else {
                $id_user = $rs->id;
                $username = $rs->username;
                $dataid_post = ($proxy != '' && $proxyauth != '') ? getMediaId($id_user, $data_login, 1, 1, $proxy, $proxyauth) : getMediaId($id_user, $data_login, 1, 1);
                foreach ($dataid_post as $id_post) {
                    $id_post = $id_post->id;
                }
                echo color()["LG"] . '[+] Username: ' . $username . ' | ';

                $sleep = $sleep;
                if ($r == 'y') {
                    $re = rand(0, 1);
                }
                if ($f == 'y') {
                    $data_follow = ($proxy != '' && $proxyauth != '') ? followByIdNew($id_user, $data_login, $proxy, $proxyauth) : followByIdNew($id_user, $data_login);
                    $flw = ($data_follow['status'] == 'success') ? print("Follow Success | ") : print("Follow Failed | ");
                } elseif ($flcdm == 'random') {
                    if ($re == '1') {
                        $data_follow = ($proxy != '' && $proxyauth != '') ? followByIdNew($id_user, $data_login, $proxy, $proxyauth) : followByIdNew($id_user, $data_login);
                        $flw = ($data_follow['status'] == 'success') ? print("Follow Success | ") : print("Follow Failed | ");
                    } else {
                        print("No Follow | ");
                    }
                } elseif ($c == '') {
                    print("No Follow | ");
                } else {
                    print("No Follow | ");
                }
                sleep(rand(2, 5));
                if ($r == 'y') {
                    $re = rand(0, 1);
                }
                if ($dataid_post['status'] == 'error') {
                    print("No Like | ");
                } else {
                    if ($l == 'y') {
                        $data_like = ($proxy != '' && $proxyauth != '') ? like($id_post, $data_login, $proxy, $proxyauth) : like($id_post, $data_login);
                        $like = ($data_like['status'] == 'success') ? print("Like Success | ") : print("Like Failed | ");
                    } elseif ($flcdm == 'random') {
                        if ($re == '1') {
                            $data_like = ($proxy != '' && $proxyauth != '') ? like($id_post, $data_login, $proxy, $proxyauth) : like($id_post, $data_login);
                            $like = ($data_like['status'] == 'success') ? print("Like Success | ") : print("Like Failed | ");
                        } else {
                            print("No Like | ");
                        }
                    } elseif ($c == '') {
                        print("No Like | ");
                    } else {
                        print("No Like | ");
                    }
                }
                sleep(rand(2, 5));
                if ($r == 'y') {
                    $re = rand(0, 1);
                }
                shuffle($x);
                $text = $x[0];
                if ($dataid_post['status'] == 'error') {
                    print("No Comment | ");
                } else {
                    if ($c == 'y') {
                        $data_comment = ($proxy != '' && $proxyauth != '') ? comment($id_post, $data_login, $text, $proxy, $proxyauth) : comment($id_post, $data_login, $text);
                        $cmnt = ($data_comment['status'] == 'success') ? print("Comment Success: $text | ") : print("Comment Failed: " . ucfirst($data_comment['details']) . " | ");
                    } elseif ($flcdm == 'random') {
                        if ($re == '1') {
                            $data_comment = ($proxy != '' && $proxyauth != '') ? comment($id_post, $data_login, $text, $proxy, $proxyauth) : comment($id_post, $data_login, $text);
                            $ccmnt = ($data_comment['status'] == 'success') ? print("Comment Success: $text | ") : print("Comment Failed: " . ucfirst($data_comment['details']) . " | ");
                        } else {
                            print("No Comment | ");
                        }
                    } elseif ($c == '') {
                        print("No Comment | ");
                    } else {
                        print("No Comment | ");
                    }
                }
                sleep(rand(2, 5));
                if ($r == 'y') {
                    $re = rand(0, 1);
                }
                shuffle($x);
                $text = $x[0];
                shuffle($link);
                $link = $link[0];
                if ($dataid_post['status'] == 'error') {
                    print("No DM | ");
                } else {
                    if ($dm == 'y') {
                        $process = ($proxy != '' && $proxyauth != '') ? ProcessDM($id_user, $data_login, $proxy, $proxyauth) : ProcessDM($id_user, $data_login);
                        $data_dm = ($proxy != '' && $proxyauth != '') ? KirimDM($text, $link, $process['thread_id'], $id_user, $data_login, $proxy, $proxyauth) : KirimDM($text, $link, $process['thread_id'], $id_user, $data_login);
                        $ccdm = ($data_dm['status'] == 'success') ? print("DM Success: $text | ") : print("DM Failed: " . ucfirst($data_comment['details']) . " | ");
                    } elseif ($flcdm == 'random') {
                        if ($re == '1') {
                            $process = ($proxy != '' && $proxyauth != '') ? ProcessDM($id_user, $data_login, $proxy, $proxyauth) : ProcessDM($id_user, $data_login);
                            $data_dm = ($proxy != '' && $proxyauth != '') ? KirimDM($text, $link, $process['thread_id'], $id_user, $data_login, $proxy, $proxyauth) : KirimDM($text, $link, $process['thread_id'], $id_user, $data_login);
                            $ccdm = ($data_comment['status'] == 'success') ? print("DM Success: $text | ") : print("DM Failed: " . ucfirst($data_comment['details']) . " | ");
                        } else {
                            print("No DM | ");
                        }
                    } elseif ($dm == '') {
                        print("No DM | ");
                    } else {
                        print("No DM | ");
                    }
                }
                sleep(rand(2, 5));
                if ($uf == '') {
                    print("No Unfollow | " . PHP_EOL);
                } else {
                    $data_unfollow = ($data_comment['status'] == 'error' && $proxy != '' && $proxyauth != '') ? unfollow($username, $data_login, $proxy, $proxyauth) ?: ($data_comment['status'] == 'error' && $proxy == '' && $proxyauth == '') ?: unfollow($username, $data_login) : '';
                    $uf = ($data_unfollow['status'] == 'success') ? print("Unfollow Success | " . PHP_EOL) : print("Unfollow Failed | " . PHP_EOL);
                }
                $delay = $sleep + rand(2, 5);
                echo "[•] Delay Selama : " . $delay . " detik" . PHP_EOL;
                sleep($delay);
            }
        }
    }
}
