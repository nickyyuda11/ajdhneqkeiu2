<?php
include 'class_ig_new_reply.php';
// error_reporting(0);
clear();
echo "
Ξ TITLE  : F-L-C Followers Target (FFT)
Ξ FLPATH : tools/fftauto.php
Ξ CODEBY : Firdy [https://fb.com/6null9]
Ξ FIXBY	 : Andi Muh. Rizqi [ikiganteng]
Ξ UPDATE : Officialputuid [26/01/2020]

";

$check = ReadSavedData();
echo "[?] Anda Memiliki Cookie yang tersimpan pilih angkanya dan gunakan kembali : " . PHP_EOL;

foreach ($check as $key => $cookie) {
    echo "[{$key}] " . $cookie['username'] . PHP_EOL;

    $data_cookie[] = $key;
}

echo "[x] Masuk menggunakan akun baru" . PHP_EOL;

echo "[?] Pilihan Anda : ";

$input = strtolower(trim(fgets(STDIN)));

if ($input != 'x') {

    if (strval($input) !== strval(intval($input))) {
        die("Salah memasukan format, pastikan hanya angka");
    }

    if (!in_array($input, $data_cookie)) {
        die("Pilihan tidak ditemukan");
    }

    $results = ReadData($input);
    $results = CheckLiveCookie($results);
    if ($results['status'] == 'success') {
        Run($results);
    }
} else {
    $u = getUsername();
    $p = getPassword();
    echo PHP_EOL;
    $results = ($proxy != '' && $proxyauth != '') ? logins($u, $p, $proxy, $proxyauth) : logins($u, $p);
    if ($results['status'] == 'success') {
        SaveData($results);
        Run($results);
    }
}

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
        if ($c == 'y' or 'Y') {
            $text_comment = getComment('[?] Enter your file comment here: ');
            $getfile = file_get_contents($text_comment);
            $x = explode("|", $getfile);
            $c = count($x) - 1;
        }
        echo '[?] DM User? (Enter Y or y for YES, Enter any key for NO): ';
        $dm = trim(fgets(STDIN));
        if ($dm == 'y' or 'Y') {
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
    $target = getUsername('[?] Enter Link Media Target: ');
    $target_p = explode("\n", $target);
    $target_c = ceil(count($target_p / 14));
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

    for ($i = 0; $i <= $target_c - 1; $i++) {
        // $data_dm = [
        //     "u_1" => $target_p[1 + 31 * $i],
        //     'u_2' => $target_p[2 + 31 * $i],
        //     'u_3' => $target_p[3 + 31 * $i],
        //     'u_4' => $target_p[4 + 31 * $i],
        //     'u_5' => $target_p[5 + 31 * $i],
        //     'u_6' => $target_p[6 + 31 * $i],
        //     'u_7' => $target_p[7 + 31 * $i],
        //     'u_8' => $target_p[8 + 31 * $i],
        //     'u_9' => $target_p[9 + 31 * $i],
        //     'u_10' => $target_p[10 + 31 * $i],
        //     'u_11' => $target_p[11 + 31 * $i],
        //     'u_12' => $target_p[12 + 31 * $i],
        //     'u_13' => $target_p[13 + 31 * $i],
        //     'u_14' => $target_p[14 + 31 * $i],
        //     'u_15' => $target_p[15 + 31 * $i],
        //     'u_16' => $target_p[16 + 31 * $i],
        //     'u_17' => $target_p[17 + 31 * $i],
        //     'u_18' => $target_p[18 + 31 * $i],
        //     'u_19' => $target_p[19 + 31 * $i],
        //     'u_20' => $target_p[20 + 31 * $i],
        //     'u_21' => $target_p[21 + 31 * $i],
        //     'u_22' => $target_p[22 + 31 * $i],
        //     'u_23' => $target_p[23 + 31 * $i],
        //     'u_24' => $target_p[24 + 31 * $i],
        //     'u_25' => $target_p[25 + 31 * $i],
        //     'u_26' => $target_p[26 + 31 * $i],
        //     'u_27' => $target_p[27 + 31 * $i],
        //     'u_28' => $target_p[28 + 31 * $i],
        //     'u_29' => $target_p[29 + 31 * $i],
        //     'u_30' => $target_p[30 + 31 * $i],
        //     'u_31' => $target_p[31 + 31 * $i],
        // ];
        $data_dm = [
            "u_1" => $target_p[1 + 14 * $i],
            'u_2' => $target_p[2 + 14 * $i],
            'u_3' => $target_p[3 + 14 * $i],
            'u_4' => $target_p[4 + 14 * $i],
            'u_5' => $target_p[5 + 14 * $i],
            'u_6' => $target_p[6 + 14 * $i],
            'u_7' => $target_p[7 + 14 * $i],
            'u_8' => $target_p[8 + 14 * $i],
            'u_9' => $target_p[9 + 14 * $i],
            'u_10' => $target_p[10 + 14 * $i],
            'u_11' => $target_p[11 + 14 * $i],
            'u_12' => $target_p[12 + 14 * $i],
            'u_13' => $target_p[13 + 14 * $i],
            'u_14' => $target_p[14 + 14 * $i],
        ];
        $dataid_post = ($proxy != '' && $proxyauth != '') ? getMediaId($id_user, $data_login, 1, 1, $proxy, $proxyauth) : getMediaId($id_user, $data_login, 1, 1);
        foreach ($dataid_post as $id_post) {
            $id_post = $id_post->id;
            var_dump($id_post);
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
                if ($data_comment['details'] == '{"message":"Unable to post comment.","status":"fail"}') {
                    $c = '';
                }
            } elseif ($flcdm == 'random') {
                if ($re == '1') {
                    $data_comment = ($proxy != '' && $proxyauth != '') ? comment($id_post, $data_login, $text, $proxy, $proxyauth) : comment($id_post, $data_login, $text);
                    $ccmnt = ($data_comment['status'] == 'success') ? print("Comment Success: $text | ") : print("Comment Failed: " . ucfirst($data_comment['details']) . " | ");
                    if ($data_comment['details'] == '{"message":"Unable to post comment.","status":"fail"}') {
                        $c = '';
                    }
                } else {
                    print("No Comment | ");
                }
            } elseif ($c == '') {
                print("No Comment | ");
            } else {
                print("No Comment | ");
            }
        }
        shuffle($text_dm_p);
        $text_dm_s = $text_dm_p[0];
        shuffle($link);
        $links = $link[0];
        if ($dataid_post['status'] == 'error') {
            print("No DM | ");
        } else {
            if ($dm == 'y') {
                $process = ($proxy != '' && $proxyauth != '') ? ProcessDMGroup($data_dm, $data_login, $proxy, $proxyauth) : ProcessDMGroup($data_dm, $data_login);
                $data_dm = ($proxy != '' && $proxyauth != '') ? KirimDMGroup($text_dm_s, $links, $process['thread_id'], $data_dm, $data_login, $proxy, $proxyauth) : KirimDMGroup($text_dm_s, $links, $process['thread_id'], $data_dm, $data_login);
                $ccdm = ($data_dm['status'] == 'success') ? print("DM Success: $text_dm_s | ") : print("DM Failed: " . ucfirst($data_comment['details']) . " | ");
            } elseif ($flcdm == 'random') {
                if ($re == '1') {
                    $process = ProcessDM($id_user, $data_login);
                    $data_dm = ($proxy != '' && $proxyauth != '') ? KirimDMGroup($text_dm_s, $links, $process['thread_id'], $data_dm, $data_login, $proxy, $proxyauth) : KirimDMGroup($text_dm_s, $links, $process['thread_id'], $data_dm, $data_login);
                    $ccdm = ($data_comment['status'] == 'success') ? print("DM Success: $text_dm_s | ") : print("DM Failed: " . ucfirst($data_comment['details']) . " | ");
                } else {
                    print("No DM | ");
                }
            } elseif ($dm == '') {
                print("No DM | ");
            } else {
                print("No DM | ");
            }
        }
        sleep($sleep + rand(1, 3));
    }
}
