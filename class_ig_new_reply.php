<?php

define("OS", strtolower(PHP_OS));
header('Content-Type: text/html; charset=utf-8');
error_reporting(0);

function generateSignature($data)
{
    // ac129560d96023898d85aff6ee861218ff504ab34848a09747a3f0987439de0f
    $hash = hash_hmac('sha256', $data, 'b4946d296abf005163e72346a6d33dd083cadde638e6ad9c5eb92e381b35784a');
    return 'ig_sig_key_version=4&signed_body=' . $hash . '.' . urlencode($data);
}

function GenerateSignatureNew($data)
{
    $instaSignature = '469862b7e45f078550a0db3687f51ef03005573121a3a7e8d7f43eddb3584a36';
    return hash_hmac('sha256', $data, $instaSignature);
}

function CheckLiveCookie($session)
{
    $url = 'https://www.instagram.com/accounts/edit/?__a=1';
    $header = array(
        'Cookie: sessionid=' . $session['sessionid'] . ';',
    );
    $get = curl($url, 0, $header);
    $first_name = getStr($get, '{"first_name":"', '","');
    $last_name = getStr($get, '"last_name":"', '","');
    $email = getStr($get, 'email":"', '","');
    $username = getStr($get, '"username":"', '","');
    $bio = getStr($get, 'biography":"', '","');
    if (strpos($get, 'Location: https://www.instagram.com/challenge/')) {
        $data = array(
            'status' => 'challenge',
        );
    } elseif (strpos($get, 'Location: https://www.instagram.com/accounts/login/?next=/accounts/edit/')) {
        $data = array(
            'status' => 'dead cookie',
        );
    } elseif (strpos($get, '301')) {
        $data = array(
            'status' => 'dead',
        );
    } elseif (strpos($get, '200')) {
        $data = array(
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'username' => $username,
            'bio' => $bio,
            'status' => 'success',
        );
    } else {
        $data = array(
            'status' => 'error',
        );
    }

    return $data;
}

function ReadData($data)
{

    $filename = 'data/user.json';

    if (file_exists($filename)) {
        $read = file_get_contents($filename);
        $read = json_decode($read, TRUE);
        foreach ($read as $key => $logdata) {
            if ($key == $data) {
                $inputdata = $logdata;
                break;
            }
        }

        return $inputdata;
    } else {
        die("file tidak ditemukan");
    }
}

function SaveData($data)
{

    $filename = 'data/user.json';

    if (file_exists($filename)) {
        $read = file_get_contents($filename);
        $read = json_decode($read, true);
        $dataexist = false;
        foreach ($read as $key => $logdata) {
            if ($logdata['ds_user_id'] == $data['ds_user_id']) {
                $inputdata[] = $data;
                $dataexist = true;
            } else {
                $inputdata[] = $logdata;
            }
        }

        if (!$dataexist) {
            $inputdata[] = $data;
        }
    } else {
        $inputdata[] = $data;
    }

    return file_put_contents($filename, json_encode($inputdata, JSON_PRETTY_PRINT));
}

function ReadSavedData()
{

    $filename = 'data/user.json';

    if (file_exists($filename)) {
        $read = file_get_contents($filename);
        $read = json_decode($read, TRUE);
        foreach ($read as $key => $logdata) {
            $inputdata[] = $logdata;
        }

        return $inputdata;
    } else {
        return false;
    }
}

function GenerateGuid()
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 65535),
        mt_rand(0, 65535),
        mt_rand(0, 65535),
        mt_rand(16384, 20479),
        mt_rand(32768, 49151),
        mt_rand(0, 65535),
        mt_rand(0, 65535),
        mt_rand(0, 65535)
    );
}

function getStr($string, $start, $end)
{
    $str = explode($start, $string);
    $str = explode($end, $str[1]);
    return $str[0];
}

function RandStr($randstr)
{
    $char = 'qwertyuiopasdfghjklzxcvbnm';
    $char .= 'QWERTYUIOPASDFGHJKLZXCVBNM';
    $char .= '0123456789';

    $str = '';
    for ($i = 0; $i < $randstr; $i++) {
        $pos = rand(0, strlen($char) - 1);
        $str .= $char{
            $pos};
    }
    return $str;
}
function RandInt($randstr)
{
    $char = '0123456789';
    $str = '';
    for ($i = 0; $i < $randstr; $i++) {
        $pos = rand(0, strlen($char) - 1);
        $str .= $char{
            $pos};
    }
    return $str;
}

/* function copycat()
{

    ## Official Copyright by Firdy [https://fb.com/6null9] ##
 
    $data = 'IyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjCiAKIC8kJCQkJCQgIC8kJCQkJCQgIC8kJCQkJCQkJCAgICAgICAgICAgICAgICAgIC8kJCAgICAgICAgICAKfF8gICQkXy8gLyQkX18gICQkfF9fICAkJF9fLyAgICAgICAgICAgICAgICAgfCAkJCAgICAgICAgICAKICB8ICQkICB8ICQkICBcX18vICAgfCAkJCAgLyQkJCQkJCAgIC8kJCQkJCQgfCAkJCAgLyQkJCQkJCQKICB8ICQkICB8ICQkIC8kJCQkICAgfCAkJCAvJCRfXyAgJCQgLyQkX18gICQkfCAkJCAvJCRfX19fXy8KICB8ICQkICB8ICQkfF8gICQkICAgfCAkJHwgJCQgIFwgJCR8ICQkICBcICQkfCAkJHwgICQkJCQkJCAKICB8ICQkICB8ICQkICBcICQkICAgfCAkJHwgJCQgIHwgJCR8ICQkICB8ICQkfCAkJCBcX19fXyAgJCQKIC8kJCQkJCR8ICAkJCQkJCQvICAgfCAkJHwgICQkJCQkJC98ICAkJCQkJCQvfCAkJCAvJCQkJCQkJC8KfF9fX19fXy8gXF9fX19fXy8gICAgfF9fLyBcX19fX19fLyAgXF9fX19fXy8gfF9fL3xfX19fX19fLyAKICAgIApNYWRlIHdpdGggTG92ZSA8MyBDb2RlIGJ5IEZpcmR5CiMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIw==';

    echo base64_decode($data).PHP_EOL;
}
*/

function curl($url, $data = 0, $header = 0, $cookie = 0, $proxy = 0, $proxyauth = 0)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    // curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    if ($header) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
    }
    if ($data) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    if ($cookie) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
    }
    if ($proxy) {
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyauth);
    }
    $x = curl_exec($ch);
    curl_close($ch);
    return $x;
}

function curlNoHeader($url, $data = 0, $header = 0, $cookie = 0, $proxy = 0, $proxyauth = 0)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

    curl_setopt($ch, CURLOPT_HEADER, 0);
    if ($header) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
    }
    if ($data) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    if ($cookie) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
    }
    if ($proxy) {
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyauth);
    }
    $x = curl_exec($ch);
    curl_close($ch);
    return $x;
}

function color()
{
    return [
        "LW" => (OS == "linux" ? "\e[1;37m" : ""),
        "WH" => (OS == "linux" ? "\e[0m" : ""),
        "LR" => (OS == "linux" ? "\e[1;31m" : ""),
        "LG" => (OS == "linux" ? "\e[1;32m" : ""),
        "BL" => (OS == "linux" ? "\e[1;34m" : ""),
        "MG" => (OS == "linux" ? "\e[1;35m" : ""),
        "LC" => (OS == "linux" ? "\e[1;36m" : ""),
        "CY" => (OS == "linux" ? "\e[1;33m" : "")
    ];
}

function clear()
{

    return (OS == "linux" ? system('clear') : "");
}

function fetchCurlCookies($source)
{
    preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $source, $matches);
    $cookies = array();
    foreach ($matches[1] as $item) {
        parse_str($item, $cookie);
        $cookies = array_merge($cookies, $cookie);
    }
    return $cookies;
}

function getIDUsername($link, $session, $proxy = 0, $proxyauth = 0)
{
    $url = 'https://www.instagram.com/web/search/topsearch/?query=' . $link . '';
    $header = array(
        'origin: https://www.instagram.com',
        'referer: https://www.instagram.com/',
        'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.146 Safari/537.36',
        'X-CSRFToken: ' . $session['csrftoken'],
        'Content-Type: application/x-www-form-urlencoded',
        'Cookie: ds_user=' . $session['username'] . ';ig_did=' . $session['ig_did'] . '; rur=FTW; mid=' . $session['mid'] . '; csrftoken=' . $session['csrftoken'] . '; ds_user_id=' . $session['ds_user_id'] . '; sessionid=' . $session['sessionid'] . ';',
    );
    if ($proxy) {
        $post = curlNoHeader($url, 0, $header, $session['username'], $proxy, $proxyauth);
    } else {
        $post = curlNoHeader($url, 0, $header, $session['username']);
    }
    $data_array = json_decode($post);
    $result = $data_array->users['0'];
    $data = array(
        'status' => 'success',
        'id' => $result->user->pk,
        'username' => $result->user->username,
    );
    return $data;
}

function getCommentFirst($link, $session, $proxy = 0, $proxyauth = 0)
{
    // if ($proxy) {
    //     # code...
    //     $profile = findProfile($link, $session, $proxy, $proxyauth);
    // }else{
    //     $profile = findProfile($link, $session);
    // }
    $url = 'https://www.instagram.com/p/' . $link . '/';
    $header = array(
        'origin: https://www.instagram.com',
        'referer: https://www.instagram.com/',
        'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.146 Safari/537.36',
        'X-CSRFToken: ' . $session['csrftoken'],
        'Content-Type: application/x-www-form-urlencoded',
        'Cookie: ds_user=' . $session['username'] . ';ig_did=' . $session['ig_did'] . '; rur=FTW; mid=' . $session['mid'] . '; csrftoken=' . $session['csrftoken'] . '; ds_user_id=' . $session['ds_user_id'] . '; sessionid=' . $session['sessionid'] . ';',
    );
    if ($proxy) {
        $post = curlNoHeader($url, 0, $header, $session['username'], $proxy, $proxyauth);
        // $ip = curl('https://api64.ipify.org/?format=json', 0, 0, 0, $proxy, $proxyauth);
        // var_dump($ip);
    } else {
        $post = curlNoHeader($url, 0, $header, $session['username']);
    }
    // $data_ouput = client_fungsi->getStr($post, "window.__additionalDataLoaded('/p/$link/',", ");</script>");
    $data_ouput = getStr($post, '{"graphql":', "});</script>");
    $data_array = json_decode($data_ouput);
    $result = $data_array;
    foreach ($result as $items) {
        $data = array(
            'status' => 'success',
            'id' => $items->id,
            'commentid' => $items->edge_media_to_parent_comment->edges['0']->node->id,
            'username' => $items->edge_media_to_parent_comment->edges['0']->node->owner->username,
        );
    }
    return $data;
}

function getCommentLink($id, $count, $after = '')
{
    if (isset($id)) {
        $url_media = 'https://www.instagram.com/graphql/query/?query_hash=bc3296d1ce80a24b1b6e40b1e72903f5&variables={"shortcode":"{{id}}","first":"{{count}}","after":"{{after}}"}';
        $url = str_replace('{{id}}', $id, $url_media);
        $url = str_replace('{{count}}', $count, $url);
        if ($after === '') {
            $url = str_replace(',"after":"{{after}}"', '', $url);
        } else {
            $url = str_replace('{{after}}', $after, $url);
            $url = str_replace('""', '"', $url);
            $url = str_replace(' ', '%20', $url);
        }
    } else {
        $url = '';
    }
    return $url;
}

function getCommentId($id, $session, $count = 20, $pageSize = 20, $proxy = 0, $proxyauth = 0)
{
    $index = 0;
    $accounts = [];
    $endCursor = '';
    $header = array();
    $header[] = 'Cookie: sessionid=' . $session['sessionid'] . ';';
    $header[] = 'X-Csrftoken: ' . $session['csrftoken'] . ';';
    if ($count < $pageSize) {

        echo 'Count must be greater than or equal to page size.' . PHP_EOL;
    }
    while (true) {

        $url = getCommentLink($id, 100, $endCursor);
        var_dump($url);
        if ($url != '') {
            if ($proxy) {
                $response = curlNoHeader($url, 0, $header, $session['username'], $proxy, $proxyauth);
            } else {
                $response = curlNoHeader($url, 0, $header);
            }
        }

        $data = json_decode($response);
        $pageInfo = $data->data->shortcode_media->edge_media_to_parent_comment;

        if ($pageInfo->count == 0) {
            $data = array(
                'status' => 'error'
            );
            return $data;
        }
        if ($pageInfo->edges == 0) {

            echo 'Failed to get followers of ' . $id . '. The account is private.';
        }

        foreach ($pageInfo->edges as $edge) {
            $accounts[] = $edge->node;
            $index++;
            if ($index >= $count) {
                break 2;
            }
        }

        if ($pageInfo->page_info->has_next_page) {

            $endCursor = json_encode($pageInfo->page_info->end_cursor);
        } else {

            $endCursor = '';
            break;
        }
    }

    return $accounts;
}

function follow_andro($username, $session)
{
    if (isset($username)) {
        $guid = GenerateGuid();
        $id = $username;
        // $url = 'https://www.instagram.com/web/friendships/' . $id . '/follow/';
        $url = 'https://i.instagram.com/api/v1/friendships/create/' . $id . '/';
        $header = array(
            'origin: https://www.instagram.com',
            'referer: https://www.instagram.com/',
            'User-Agent: ' . $session['useragent'],
            'X-CSRFToken: ' . $session['csrftoken'],
            'Cookie: ig_did=' . $session['ig_did'] . '; mid=' . $session['mid'] . '; csrftoken=' . $session['csrftoken'] . '; ds_user_id=' . $session['ds_user_id'] . '; sessionid=' . $session['sessionid']
        );
        $device_id = "android-" . $guid;
        $data = '{"device_id":"' . $device_id . '","guid":"' . $guid . '","uid":"' . $session['ds_user_id'] . '","module_name":"feed_timeline","d":"0","source_type":"5","filter_type":"0","extra":"{}","Content-Type":"application/x-www-form-urlencoded; charset=UTF-8"}';
        $sig = GenerateSignature($data);
        $body = 'signed_body=' . $sig . '.' . urlencode($data) . '&ig_sig_key_version=4';
        // $body = 'ig_sig_key_version=4&signed_body=' . rand(100, 1000) . '';
        $post = curlNoHeader($url, $body, $header, true);
        if (strpos($post, 'Location: https://www.instagram.com/challenge/')) {
            $data = array(
                'username' => $username,
                'action' => 'follow',
                'status' => 'error',
            );
        } elseif (strpos($post, '"status":"ok"')) {
            $data = array(
                'username' => $username,
                'action' => 'follow',
                'status' => 'success',
            );
        } else {
            $data = array(
                'username' => $username,
                'action' => 'follow',
                'status' => 'error',
            );
        }
    } else {
        $data = array(
            'status' => 'error',
            'username' => $username,
            'action' => 'follow',
            'details' => 'username not found',
        );
    }
    return $data;
}

function like_andro($id, $session)
{
    if (isset($id)) {
        $guid = GenerateGuid();
        // $url = 'https://www.instagram.com/web/likes/' . $id . '/like/';
        $url = 'https://i.instagram.com/api/v1/media/' . $id . '/like/';
        $header = array(
            'Origin: https://www.instagram.com',
            'Referer: https://www.instagram.com/',
            'User-Agent: ' . $session['useragent'],
            'X-CSRFToken: ' . $session['csrftoken'],
            'Cookie: ig_did=' . $session['ig_did'] . '; mid=' . $session['mid'] . '; csrftoken=' . $session['csrftoken'] . '; ds_user_id=' . $session['ds_user_id'] . '; sessionid=' . $session['sessionid']
        );
        $device_id = "android-" . $guid;
        $data = '{"device_id":"' . $device_id . '","guid":"' . $guid . '","uid":"' . $session['ds_user_id'] . '","module_name":"feed_timeline","d":"0","source_type":"5","filter_type":"0","extra":"{}","Content-Type":"application/x-www-form-urlencoded; charset=UTF-8"}';
        $sig = GenerateSignature($data);
        $body = 'ig_sig_key_version=5&signed_body=' . $sig . '.' . urlencode($data) . '';
        // $body = 'ig_sig_key_version=4&signed_body=' . rand(100, 1000) . '';
        $post = curlNoHeader($url, $body, $header, true);
        $data = (strpos($post, "fail")) ? array("media" => $id, "action" => "like", "status" => "error") : array("media" => $id, "action" => "like", "status" => "success");
    } else {
        $data = array("media" => $id, "status" => "error", "details" => "media not found");
    }
    return $data;
}

function newfindProfile($username, $session = 0, $proxy = 0, $proxyauth = 0)
{
    $url = 'https://www.instagram.com/' . $username . '/?__a=1';
    if ($session != 0) {
        $header = array(
            'origin: https://www.instagram.com',
            'referer: https://www.instagram.com/',
            'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.146 Safari/537.36',
            'X-CSRFToken: ' . $session['csrftoken'],
            'Content-Type: application/x-www-form-urlencoded',
            'Cookie: ds_user=' . $session['username'] . ';ig_did=' . $session['ig_did'] . '; rur=FTW; mid=' . $session['mid'] . '; csrftoken=' . $session['csrftoken'] . '; ds_user_id=' . $session['ds_user_id'] . '; sessionid=' . $session['sessionid'] . ';',
        );
        if ($proxy) {
            $get = curlNoHeader($url, 0, $header, $session['username'], $proxy, $proxyauth);
        } else {
            $get = curlNoHeader($url, 0, $header, $session['username']);
        }
    } else {
        if ($proxy) {
            $get = curlNoHeader($url, 0, 0, $session['username'], $proxy, $proxyauth);
        } else {
            $get = curlNoHeader($url);
        }
    }
    $data = json_decode($get);
    return $data;
}

function findProfile($username, $session = 0, $proxy = 0, $proxyauth = 0)
{
    $url = 'https://instagram.com/' . $username . '/';
    if ($session != 0) {
        // 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.146 Safari/537.36',
        // 'User-Agent: ' . $session['useragent'],
        // 'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 11_0_1 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A402 Safari/604.1',
        $header = array(
            'origin: https://www.instagram.com/',
            'referer: https://www.instagram.com/',
            'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.146 Safari/537.36',
            'X-CSRFToken: ' . $session['csrftoken'],
            'Content-Type: application/x-www-form-urlencoded',
            'Cookie: ds_user=' . $session['username'] . ';ig_did=' . $session['ig_did'] . ';rur=FTW;mid=' . $session['mid'] . ';csrftoken=' . $session['csrftoken'] . ';ds_user_id=' . $session['ds_user_id'] . ';sessionid=' . $session['sessionid'] . ';',
        );
        if ($proxy) {
            $get = curlNoHeader($url, 0, $header, $session['username'], $proxy, $proxyauth);
            // $ip = curl('https://api64.ipify.org/?format=json', 0, 0, 0, $proxy, $proxyauth);
            // var_dump($ip);
        } else {
            $get = curlNoHeader($url, 0, $header, $session['username']);
        }
    } else {
        if ($proxy) {
            $get = curl($url, 0, 0, $session['username'], $proxy, $proxyauth);
        } else {
            $get = curl($url);
        }
    }
    if (strpos($get, 'The link you followed may be broken, or the page may have been removed.')) {

        $data = array(
            'status' => 'error',
            'details' => 'user not found'
        );
    } else {

        $data_ouput = getStr($get, '<script type="text/javascript">window._sharedData = ', ';</script>');
        $data_array = json_decode($data_ouput);
        $result = $data_array->entry_data->ProfilePage['0']->graphql->user;
        if (empty($result->edge_owner_to_timeline_media->edges) && $result->edge_owner_to_timeline_media->count >= 1) {
            $result = $data_array->entry_data->ProfilePage['0']->graphql->user;
            $data = array(
                'status' => 'error',
                'details' => 'account private',
                'id' => $result->id,
                'username' => $username,
                'fullname' => $result->full_name,
                'followers' => $result->edge_followed_by->count,
                'following' => $result->edge_follow->count,
                'post' => $result->edge_owner_to_timeline_media->count,
            );
        } else {

            $result = $data_array->entry_data->ProfilePage['0']->graphql->user;
            $vid = ($result->edge_owner_to_timeline_media->edges['0']->node->is_video == 1) ? "yes" : "no";
            $is_follow = ($result->followed_by_viewer) ? 'true' : 'false';
            $is_private = ($result->is_private) ? 'true' : 'false';
            $is_verified = ($result->is_verified) ? 'true' : 'false';
            $is_polbek = ($result->follows_viewer) ? 'true' : 'false';
            $data = array(
                'status' => 'success',
                'username' => $username,
                'fullname' => $result->full_name,
                'followers' => $result->edge_followed_by->count,
                'following' => $result->edge_follow->count,
                'is_follow' => $is_follow,
                'is_private' => $is_private,
                'is_polbek' => $is_polbek,
                'id' => $result->id,
                'is_verif' => $is_verified,
                'post' => $result->edge_owner_to_timeline_media->count,
                'id_media' => $result->edge_owner_to_timeline_media->edges['0']->node->id,
                'page' => 'https://instagram.com/p/' . $result->edge_owner_to_timeline_media->edges['0']->node->shortcode,
                'shortcode' => $result->edge_owner_to_timeline_media->edges['0']->node->shortcode,
                'comments' => $result->edge_owner_to_timeline_media->edges['0']->node->edge_media_to_comment->count,
                'likes' => $result->edge_owner_to_timeline_media->edges['0']->node->edge_liked_by->count,
                'url_display' => $result->edge_owner_to_timeline_media->edges['0']->node->display_url,
                'is_video' => $vid
            );
        }
    }

    return $data;
}

function getProfile($session)
{
    $url = 'https://www.instagram.com/accounts/edit/?__a=1';
    $header = array(
        'origin: https://www.instagram.com',
        'referer: https://www.instagram.com/',
        'User-Agent: ' . $session['useragent'],
        'X-CSRFToken: ' . $session['csrftoken'],
        'Content-Type: application/x-www-form-urlencoded',
        'Cookie: ig_did=' . $session['ig_did'] . '; rur=FTW; mid=' . $session['mid'] . '; csrftoken=' . $session['csrftoken'] . '; ds_user_id=' . $session['ds_user_id'] . '; sessionid=' . $session['sessionid'] . ';',
    );
    $get = curl($url, 0, $header, $session['username']);

    $first_name = getStr($get, '{"first_name":"', '","');
    $last_name = getStr($get, '"last_name":"', '","');
    $email = getStr($get, 'email":"', '","');
    $username = getStr($get, '"username":"', '","');
    $bio = getStr($get, 'biography":"', '","');

    $data = array(
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'username' => $username,
        'bio' => $bio,
    );

    return $data;
}
function generate_useragent($sign_version = '138.0.0.26.117')
{
    $resolusi = array(
        '1080x1776',
        '1080x1920',
        '720x1280',
        '320x480',
        '480x800',
        '1024x768',
        '1280x720',
        '768x1024',
        '480x320'
    );
    $versi    = array(
        'GT-N7000',
        'SM-N9000',
        'GT-I9220',
        'GT-I9100'
    );
    $dpi      = array(
        '120',
        '160',
        '320',
        '240'
    );
    $ver      = $versi[array_rand($versi)];
    $data = 'Instagram ' . $sign_version . ' Android (' . mt_rand(10, 11) . '/' . mt_rand(1, 3) . '.' . mt_rand(3, 5) . '.' . mt_rand(0, 5) . '; ' . $dpi[array_rand($dpi)] . '; ' . $resolusi[array_rand($resolusi)] . '; samsung; ' . $ver . '; ' . $ver . '; smdkc210; en_US)';
    return $data;
}

function login($username, $password, $proxy = 0, $proxyauth = 0)
{
    ## URL ##
    $url_login = 'https://www.instagram.com/accounts/login/ajax/';
    $url_ig = 'https://www.instagram.com/accounts/login/';

    ## GET COOKIE SEBELUM LOGIN ##    

    $get = curl($url_ig);

    $cookie = fetchCurlCookies($get);
    $csrf = $cookie['csrftoken'];
    $mid = $cookie['mid'];
    $ig_did = $cookie['ig_did'];

    ## HEADER LOGIN ##
    $header = array(
        'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 11_0_1 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A402 Safari/604.1',
        'X-CSRFToken: ' . $csrf,
        'Content-Type: application/x-www-form-urlencoded',
        'Cookie: rur=FTW; mid=' . $mid . '; csrftoken=' . $csrf . '',
    );

    ## BODY LOGIN ##
    $body = 'username=' . $username . '&enc_password=#PWD_INSTAGRAM_BROWSER:0:' . time() . ':' . $password . '&queryParams=%7B%7D&optIntoOneTap=false';

    ## LOGIN ##
    if ($proxy) {
        $ip = curl('https://api64.ipify.org/?format=json', 0, 0, 0, $proxy, $proxyauth);
        var_dump($ip);
        $post = curl($url_login, $body, $header, $username, $proxy, $proxyauth);
    } else {
        $post = curl($url_login, $body, $header);
    }
    var_dump($post);
    if (strpos($post, '"authenticated":false')) {

        $data = array(
            'action' => 'login',
            'status' => 'error',
            'username' => $username,
        );
    } elseif (strpos($post, '"authenticated":true')) {

        $cookie_log = fetchCurlCookies($post);
        $data = array(
            'action' => 'login',
            'status' => 'success',
            'username' => $username,
            'csrftoken' => $cookie_log['csrftoken'],
            'sessionid' => $cookie_log['sessionid'],
            'ds_user_id' => $cookie_log['ds_user_id'],
            'ig_did' => $ig_did,
            'mid' => $mid,
            'useragent' => $header['useragent']
        );
    } else {

        $data = $post;
    }

    return $data;
}

function logins($username, $password, $proxy = 0, $proxyauth = 0)
{
    ## URL ##
    $url_login = 'https://www.instagram.com/accounts/login/ajax/';
    $url_ig = 'https://www.instagram.com/accounts/login/';

    ## GET COOKIE SEBELUM LOGIN ##    
    // if ($proxy) {
    //     $get = curl($url_ig, 1, 1, $session['username'], $proxy, $proxyauth);
    //     var_dump($get);
    // } else {
    $get = curl($url_ig);
    // }

    $cookie = fetchCurlCookies($get);
    $useragent = generate_useragent();
    $csrf = $cookie['csrftoken'];
    $mid = $cookie['mid'];
    $ig_did = $cookie['ig_did'];

    ## HEADER LOGIN ##
    $header = array(
        'origin: https://www.instagram.com',
        'referer: https://www.instagram.com/',
        'User-Agent: ' . $useragent,
        'X-CSRFToken: ' . $csrf,
        'Content-Type: application/x-www-form-urlencoded',
        'Cookie: ig_did=' . $ig_did . '; rur=FTW; mid=' . $mid . '; csrftoken=' . $csrf . '',
    );

    ## BODY LOGIN ##
    $body = 'username=' . $username . '&enc_password=#PWD_INSTAGRAM_BROWSER:0:' . time() . ':' . $password . '&queryParams=%7B%7D&optIntoOneTap=false';

    ## LOGIN ##
    if ($proxy) {
        $ip = curl('https://api64.ipify.org/?format=json', 0, 0, 0, $proxy, $proxyauth);
        var_dump($ip);
        $post = curl($url_login, $body, $header, $username, $proxy, $proxyauth);
    } else {
        $post = curl($url_login, $body, $header);
    }

    if (strpos($post, '"authenticated":false')) {

        $data = array(
            'action' => 'login',
            'status' => 'error',
            'username' => $username,
        );
    } elseif (strpos($post, '"authenticated":true')) {

        $cookie_log = fetchCurlCookies($post);
        // return [
        //     'status' => 'success',
        //     'response' => [
        //         'userid' => $userid,
        //         'username' => $userinfo['response']['username'],
        //         'photo' => $userinfo['response']['photo'],
        //         'cookie' => $cookie,
        //         'csrftoken' => $csrftoken,
        //         'uuid' => $guid,
        //         'cookiepath' => $cookiepath
        //     ]
        // ];
        $data = array(
            'action' => 'login',
            'status' => 'success',
            'username' => $username,
            'csrftoken' => $cookie_log['csrftoken'],
            'sessionid' => $cookie_log['sessionid'],
            'ds_user_id' => $cookie_log['ds_user_id'],
            'ig_did' => $ig_did,
            'mid' => $mid,
            'useragent' => $useragent
        );
    } else {

        $data = $post;
    }

    return $data;
}

function comment_andro($id, $session, $text)
{
    // $url = 'https://www.instagram.com/web/comments/' . $id . '/add/';
    $url = 'https://i.instagram.com/api/v1/media/' . $id . '/comment/';
    $guid = GenerateGuid();
    $header = array(
        'origin: https://www.instagram.com',
        'referer: https://www.instagram.com/',
        'User-Agent: ' . $session['useragent'],
        'X-CSRFToken: ' . $session['csrftoken'],
        'Cookie: ig_did=' . $session['ig_did'] . '; mid=' . $session['mid'] . '; csrftoken=' . $session['csrftoken'] . '; ds_user_id=' . $session['ds_user_id'] . '; sessionid=' . $session['sessionid']
    );
    // $body = 'comment_text='.$text.'&replied_to_comment_id=17854158589878259';
    // $body = 'comment_text=' . $text . '&replied_to_comment_id=';
    $device_id = "android-" . $guid;
    $data = '{"device_id":"' . $device_id . '","guid":"' . $guid . '","uid":"' . $session['ds_user_id'] . '", "comment_text": "' . $text . '","module_name":"feed_timeline","d":"0","source_type":"5","filter_type":"0","extra":"{}","Content-Type":"application/x-www-form-urlencoded; charset=UTF-8"}';
    $sig = GenerateSignatureNew($data);
    $body = 'ig_sig_key_version=5&signed_body=' . $sig . '.' . urlencode($data) . '';
    $post = curlNoHeader($url, $body, $header, true);
    $result = json_decode($post);

    if (strpos($post, 'Please wait')) {
        $data = array(
            'action' => 'comment',
            'status' => 'error',
            'details' => 'Please wait a few minutes before you try again'
        );
    } elseif (strpos($post, '"status":"ok"')) {
        $data = array(
            'action' => 'comment',
            'status' => 'success',
        );
    } else {
        $data = array(
            'action' => 'comment',
            'status' => 'error',
            'details' => $post
        );
    }
    return $data;
}

function comment($id, $session, $text, $proxy = 0, $proxyauth = 0)
{
    $url = 'https://www.instagram.com/web/comments/' . $id . '/add/';
    $header = array(
        'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 11_0_1 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A402 Safari/604.1',
        'X-CSRFToken: ' . $session['csrftoken'],
        'Content-Type: application/x-www-form-urlencoded',
        'Cookie: ds_user=' . $session['username'] . ';ig_did=' . $session['ig_did'] . '; rur=FTW; mid=' . $session['mid'] . '; csrftoken=' . $session['csrftoken'] . '; ds_user_id=' . $session['ds_user_id'] . '; sessionid=' . $session['sessionid'] . ';',
    );
    $body = 'comment_text=' . $text . '&replied_to_comment_id=';
    if ($proxy) {
        $post = curlNoHeader($url, $body, $header, $session['username'], $proxy, $proxyauth);
    } else {
        $post = curlNoHeader($url, $body, $header, $session['username']);
    }
    $result = json_decode($post);
    if (strpos($post, 'Please wait')) {

        $data = array(
            'action' => 'comment',
            'status' => 'error',
            'details' => 'Please wait a few minutes before you try again'
        );
    } elseif (strpos($post, '"status":"ok"')) {

        $data = array(
            'action' => 'comment',
            'status' => 'success',
            'text' => $result->text
        );
    } else {

        $data = array(
            'action' => 'comment',
            'status' => 'error',
            'details' => $post
        );
    }


    return $data;
}

function ReplyComment($id, $id_comment, $session, $username, $text, $proxy = 0, $proxyauth = 0)
{
    $url = 'https://www.instagram.com/web/comments/' . $id . '/add/';
    $header = array(
        'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 11_0_1 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A402 Safari/604.1',
        'X-CSRFToken: ' . $session['csrftoken'],
        'Content-Type: application/x-www-form-urlencoded',
        'Cookie: ds_user=' . $session['username'] . ';ig_did=' . $session['ig_did'] . '; rur=FTW; mid=' . $session['mid'] . '; csrftoken=' . $session['csrftoken'] . '; ds_user_id=' . $session['ds_user_id'] . '; sessionid=' . $session['sessionid'] . ';',
    );
    if ($id_comment != NULL) {
        $body = 'comment_text=@' . $username . ' ' . $text . '&replied_to_comment_id=' . $id_comment . '';
    } else {
        $body = 'comment_text=' . $text . '&replied_to_comment_id=';
    }
    if ($proxy) {
        $post = curlNoHeader($url, $body, $header, $session['username'], $proxy, $proxyauth);
    } else {
        $post = curlNoHeader($url, $body, $header, $session['username']);
    }
    $result = json_decode($post);
    if (strpos($post, 'Please wait')) {

        $data = array(
            'action' => 'comment',
            'status' => 'error',
            'details' => 'Please wait a few minutes before you try again'
        );
    } elseif (strpos($post, '"status":"ok"')) {

        $data = array(
            'action' => 'comment',
            'status' => 'success',
            'text' => $result->text
        );
    } else {

        $data = array(
            'action' => 'comment',
            'status' => 'error',
            'details' => $post
        );
    }


    return $data;
}

function like($id, $session, $proxy = 0, $proxyauth = 0)
{
    if (isset($id)) {
        $url = 'https://www.instagram.com/web/likes/' . $id . '/like/';
        $header = array(
            'origin: https://www.instagram.com',
            'referer: https://www.instagram.com/',
            'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.146 Safari/537.36',
            'X-CSRFToken: ' . $session['csrftoken'],
            'Content-Type: application/x-www-form-urlencoded',
            'Cookie: ds_user=' . $session['username'] . ';ig_did=' . $session['ig_did'] . '; rur=FTW; mid=' . $session['mid'] . '; csrftoken=' . $session['csrftoken'] . '; ds_user_id=' . $session['ds_user_id'] . '; sessionid=' . $session['sessionid'] . ';',
        );
        if ($proxy) {
            $post = curl($url, 1, $header, $session['username'], $proxy, $proxyauth);
        } else {
            $post = curl($url, 1, $header);
        }
        $data = (strpos($post, "Error")) ? array("media" => $id, "action" => "like", "status" => "error") : array("media" => $id, "action" => "like", "status" => "success");
    } else {

        $data = array("media" => $id, "status" => "error", "details" => "media not found");
    }

    return $data;
}


function unlike($id, $session, $proxy = 0, $proxyauth = 0)
{
    if (isset($id)) {
        $url = 'https://www.instagram.com/web/likes/' . $id . '/unlike/';
        $header = array(
            'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 11_0_1 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A402 Safari/604.1',
            'X-CSRFToken: ' . $session['csrftoken'],
            'Cookie: csrftoken=' . $session['csrftoken'] . '; sessionid=' . $session['sessionid']
        );
        if ($proxy) {
            $post = curl($url, 0, $header, $session['username'], $proxy, $proxyauth);
        } else {
            $post = curl($url, 0, $header, $session['username']);
        }

        $data = (strpos($post, "Error")) ? array("media" => $id, "action" => "unlike", "status" => "error") : array("media" => $id, "action" => "unlike", "status" => "success");
    } else {

        $data = array("media" => $id, "status" => "error", "details" => "media not found");
    }

    return $data;
}

function UnfollowByID($id, $session, $proxy = 0, $proxyauth = 0)
{
    if ($id) {
        $url = 'https://www.instagram.com/web/friendships/' . $id . '/unfollow/';
        $header = array(
            'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 11_0_1 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A402 Safari/604.1',
            'X-CSRFToken: ' . $session['csrftoken'],
            'Cookie: csrftoken=' . $session['csrftoken'] . '; sessionid=' . $session['sessionid']
        );
        if ($proxy) {
            $post = curl($url, 0, $header, $session['username'], $proxy, $proxyauth);
        } else {
            $post = curl($url, 0, $header, $session['username']);
        }

        $data = (strpos($post, "Error")) ? array("username" => $id, "action" => "unfollow", "status" => "error") : array("username" => $id, "action" => "unfollow", "status" => "success");
    } else {

        $data = array("username" => $id, "status" => "error", "details" => "username not found");
    }

    return $data;
}

function unfollow($username, $session, $proxy = 0, $proxyauth = 0)
{
    if ($proxy) {
        $profile = findProfile($username, $session, $proxy, $proxyauth);
    } else {
        $profile = findProfile($username, $session);
    }
    if (isset($profile['id'])) {
        $id = $profile['id'];
        $url = 'https://www.instagram.com/web/friendships/' . $id . '/unfollow/';
        $header = array(
            'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 11_0_1 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A402 Safari/604.1',
            'X-CSRFToken: ' . $session['csrftoken'],
            'Cookie: csrftoken=' . $session['csrftoken'] . '; sessionid=' . $session['sessionid']
        );
        if ($proxy) {
            $post = curl($url, 0, $header, $session['username'], $proxy, $proxyauth);
        } else {
            $post = curl($url, 0, $header, $session['username']);
        }

        $data = (strpos($post, "Error")) ? array("username" => $username, "action" => "unfollow", "status" => "error") : array("username" => $username, "action" => "unfollow", "status" => "success");
    } else {

        $data = array("username" => $username, "status" => "error", "details" => "username not found");
    }

    return $data;
}


function follow($username, $session, $proxy = 0, $proxyauth = 0)
{
    if ($proxy) {
        $profile = findProfile($username, $session, $proxy, $proxyauth);
    } else {
        $profile = findProfile($username, $session);
    }
    if (isset($profile['id'])) {

        $id = $profile['id'];
        $url = 'https://www.instagram.com/web/friendships/' . $id . '/follow/';
        $header = array(
            'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 11_0_1 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A402 Safari/604.1',
            'X-CSRFToken: ' . $session['csrftoken'],
            'Cookie: csrftoken=' . $session['csrftoken'] . '; sessionid=' . $session['sessionid']
        );
        if ($profile['is_follow'] == 'true') {

            $data = array(
                'status' => 'error',
                'details' => 'already follow'
            );
        } else {
            if ($proxy) {
                $post = curl($url, 0, $header, $session['username'], $proxy, $proxyauth);
            } else {
                $post = curl($url, 0, $header, $session['username']);
            }
            $data_ouput = getStr($post, '<window.__additionalDataLoaded type="text/javascript">window._sharedData = ', ';</window.__additionalDataLoaded>');
            $data_array = json_decode($data_ouput);
            $result = $data_array->entry_data->ProfilePage['0']->graphql->user;
            $is_follow = ($result->followed_by_viewer) ? 'true' : 'false';
            if (strpos($post, 'Location: https://www.instagram.com/accounts/login/') && strpos($post, '302 Found')) {

                $data = array(
                    'status' => 'error',
                    'details' => 'you are note logged in'
                );
            } elseif ($is_follow == 'true') {

                $data = array(
                    'username' => $username,
                    'action' => 'follow',
                    'status' => 'success',
                );
            } else {
                $a = getStr($post, '{', '}');
                $b = '{' . $a . '}';

                $data = array(
                    'username' => $username,
                    'action' => 'follow',
                    'status' => 'error',
                    'details' => $b,
                );
            }
        }
    } else {


        $data = array(
            'status' => 'error',
            'username' => $username,
            'action' => 'follow',
            'details' => 'username not found',
        );
    }

    return $data;
}

function KirimDMGroup($message, $link, $thread_ids, $userids, $session)
{

    $url = "https://i.instagram.com/api/v1/direct_v2/threads/broadcast/link/";

    $header = array(
        'User-Agent: ' . $session['useragent'],
        'X-CSRFToken: ' . $session['csrftoken'],
        'Content-Type: application/x-www-form-urlencoded',
        'Cookie: ds_user=' . $session['username'] . ';ig_did=' . $session['ig_did'] . '; rur=FTW; mid=' . $session['mid'] . '; csrftoken=' . $session['csrftoken'] . '; ds_user_id=' . $session['ds_user_id'] . '; sessionid=' . $session['sessionid'] . ';',
    );
    // $body = 'link_text=' . $message . ' ' . $link . '&_uuid=&_csrftoken=' . $session['csrftoken'] . '&recipient_users=[["' . $userids['u_1'] . '"]]&action=send_item&thread_ids["' . $thread_ids . ',"' . $userids['u_2'] . '","' . $userids['u_3'] . '","' . $userids['u_4'] . '","' . $userids['u_5'] . '","' . $userids['u_6'] . '","' . $userids['u_7'] . '","' . $userids['u_8'] . '","' . $userids['u_9'] . '","' . $userids['u_10'] . '","' . $userids['u_11'] . '","' . $userids['u_12'] . '","' . $userids['u_13'] . '","' . $userids['u_14'] . '"]&link_urls=["' . $link . '"]';
    $body = 'link_text=' . $message . '' . $link . '&_uuid=&_csrftoken=' . $session['csrftoken'] . '&recipient_users=[["' . $userids['u_1'] . '","' . $userids['u_2'] . '","' . $userids['u_3'] . '","' . $userids['u_4'] . '","' . $userids['u_5'] . '","' . $userids['u_6'] . '","' . $userids['u_7'] . '","' . $userids['u_8'] . '","' . $userids['u_9'] . '","' . $userids['u_10'] . '","' . $userids['u_11'] . '","' . $userids['u_12'] . '","' . $userids['u_13'] . '","' . $userids['u_14'] . '"]]&action=send_item&thread_ids["' . $thread_ids . '"]&link_urls=["' . $link . '"]';
    // $body = 'link_text=' . $message . '' . $link . '&_uuid=&_csrftoken=' . $session['csrftoken'] . '&recipient_users=[["' . $userids['u_1'] . '","' . $userids['u_2'] . '","' . $userids['u_3'] . '","' . $userids['u_4'] . '","' . $userids['u_5'] . '","' . $userids['u_6'] . '","' . $userids['u_7'] . '","' . $userids['u_8'] . '","' . $userids['u_9'] . '","' . $userids['u_10'] . '","' . $userids['u_11'] . '","' . $userids['u_12'] . '","' . $userids['u_13'] . '","' . $userids['u_14'] . '","' . $userids['u_15'] . '","' . $userids['u_16'] . '","' . $userids['u_17'] . '","' . $userids['u_18'] . '","' . $userids['u_19'] . '","' . $userids['u_20'] . '","' . $userids['u_21'] . '","' . $userids['u_22'] . '","' . $userids['u_23'] . '","' . $userids['u_24'] . '","' . $userids['u_25'] . '","' . $userids['u_26'] . '","' . $userids['u_27'] . '","' . $userids['u_28'] . ',"' . $userids['u_29'] . ',"' . $userids['u_30'] . ',"' . $userids['u_31'] . '"]]&action=send_item&thread_ids["' . $thread_ids . '"]&link_urls=["' . $link . '"]';
    $post = curlNoHeader($url, $body, $header, $session['username']);
    $response = json_decode($post, true);

    if ($response['status'] == 'ok') {
        return [
            'status' => true,
            'response' => 'success send message ' . $message,
        ];
    } else {
        return [
            'status' => false,
            'response' => $post['body']
        ];
    }
}

function KirimDM($message, $link, $thread_ids, $userids, $session)
{

    $url = "https://i.instagram.com/api/v1/direct_v2/threads/broadcast/link/";

    $header = array(
        'User-Agent: ' . $session['useragent'],
        'X-CSRFToken: ' . $session['csrftoken'],
        'Content-Type: application/x-www-form-urlencoded',
        'Cookie: ds_user=' . $session['username'] . ';ig_did=' . $session['ig_did'] . '; rur=FTW; mid=' . $session['mid'] . '; csrftoken=' . $session['csrftoken'] . '; ds_user_id=' . $session['ds_user_id'] . '; sessionid=' . $session['sessionid'] . ';',
    );
    $body = 'link_text=' . $message . ' ' . $link . '&_uuid=&_csrftoken=' . $session['csrftoken'] . '&recipient_users=[["' . $userids . '"]]&action=send_item&thread_ids["' . $thread_ids . '"]&link_urls=["' . $link . '"]';
    $post = curlNoHeader($url, $body, $header, $session['username']);
    $response = json_decode($post, true);

    if ($response['status'] == 'ok') {
        return [
            'status' => true,
            'response' => 'success send message ' . $message,
        ];
    } else {
        return [
            'status' => false,
            'response' => $post['body']
        ];
    }
}

function ProcessDMGroup($userids, $session, $proxy = 0, $proxyauth = 0)
{
    $url = "https://i.instagram.com/api/v1/direct_v2/create_group_thread/";

    $header = array(
        'User-Agent: ' . $session['useragent'],
        'X-CSRFToken: ' . $session['csrftoken'],
        'Content-Type: application/x-www-form-urlencoded',
        'Cookie: ds_user=' . $session['username'] . ';ig_did=' . $session['ig_did'] . '; rur=FTW; mid=' . $session['mid'] . '; csrftoken=' . $session['csrftoken'] . '; ds_user_id=' . $session['ds_user_id'] . '; sessionid=' . $session['sessionid'] . ';',
    );
    // $body = 'recipient_users=["' . $userids['u_1'] . '"]';

    $body = 'recipient_users=["' . $userids['u_1'] . '","' . $userids['u_2'] . '","' . $userids['u_3'] . '","' . $userids['u_4'] . '","' . $userids['u_5'] . '","' . $userids['u_6'] . '","' . $userids['u_7'] . '","' . $userids['u_8'] . '","' . $userids['u_9'] . '","' . $userids['u_10'] . '","' . $userids['u_11'] . '","' . $userids['u_12'] . '","' . $userids['u_13'] . '","' . $userids['u_14'] . '"]';
    if ($proxy) {
        $post = curlNoHeader($url, $body, $header, $session['username'], $proxy, $proxyauth);
    } else {
        $post = curlNoHeader($url, $body, $header, $session['username']);
    }

    $response = json_decode($post, true);

    if ($response['status'] == 'ok') {
        return [
            'status' => true,
            'thread_id' => $response['thread_id']
        ];
    } else {
        return [
            'status' => false,
            'response' => $post['body']
        ];
    }
}

function ProcessDM($userids, $session, $proxy = 0, $proxyauth = 0)
{
    $url = "https://i.instagram.com/api/v1/direct_v2/create_group_thread/";

    $header = array(
        'User-Agent: ' . $session['useragent'],
        'X-CSRFToken: ' . $session['csrftoken'],
        'Content-Type: application/x-www-form-urlencoded',
        'Cookie: ds_user=' . $session['username'] . ';ig_did=' . $session['ig_did'] . '; rur=FTW; mid=' . $session['mid'] . '; csrftoken=' . $session['csrftoken'] . '; ds_user_id=' . $session['ds_user_id'] . '; sessionid=' . $session['sessionid'] . ';',
    );
    $body = 'recipient_users=["' . $userids . '"]';
    if ($proxy) {
        $post = curlNoHeader($url, $body, $header, $session['username'], $proxy, $proxyauth);
    } else {
        $post = curlNoHeader($url, $body, $header, $session['username']);
    }

    $response = json_decode($post, true);

    if ($response['status'] == 'ok') {
        return [
            'status' => true,
            'thread_id' => $response['thread_id']
        ];
    } else {
        return [
            'status' => false,
            'response' => $post['body']
        ];
    }
}

function followByIdNew($id, $session, $proxy = 0, $proxyauth = 0)
{
    $url = 'https://www.instagram.com/web/friendships/' . $id . '/follow/';
    $username = $id;
    $header = array(
        'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 11_0_1 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A402 Safari/604.1',
        'X-CSRFToken: ' . $session['csrftoken'],
        'Cookie: csrftoken=' . $session['csrftoken'] . '; sessionid=' . $session['sessionid']
    );

    if ($proxy) {
        $post = curl($url, $session['username'], $header, $session['username'], $proxy, $proxyauth);
    } else {
        $post = curl($url, $session['username'], $header, $session['username']);
    }
    $data_ouput = getStr($post, '<script type="text/javascript">window._sharedData = ', ';</script>');
    $data_array = json_decode($data_ouput);
    $result = $data_array->entry_data->ProfilePage['0']->graphql->user;
    $is_follow = ($result->followed_by_viewer) ? 'true' : 'false';
    if (strpos($post, 'Location: https://www.instagram.com/accounts/login/') && strpos($post, '302 Found')) {

        $data = array(
            'status' => 'error',
            'details' => 'you are note logged in'
        );
    } elseif ($is_follow == 'true') {

        $data = array(
            'username' => $username,
            'action' => 'follow',
            'status' => 'success',
        );
    } else {
        $a = getStr($post, '{', '}');
        $b = '{' . $a . '}';

        $data = array(
            'username' => $username,
            'action' => 'follow',
            'status' => 'error',
            'details' => $b,
        );
    }
    return $data;
}

function followById($id, $session, $proxy = 0, $proxyauth = 0)
{
    $url_info = 'https://i.instagram.com/api/v1/users/' . $id . '/info/';
    if ($proxy) {
        $info = curlNoHeader($url_info, 0, 0, 0, $proxy, $proxyauth);
    } else {
        $info = curlNoHeader($url_info);
    }
    $data_info = json_decode($info);
    $username = $data_info->user->username;

    if (strpos($info, 'Page Not Found')) {

        $data = array(
            'status' => 'error',
            'username' => $username,
            'action' => 'follow',
            'details' => 'username not found',
        );
    } else {

        $url = 'https://www.instagram.com/web/friendships/' . $id . '/follow/';
        $header = array(
            'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 11_0_1 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A402 Safari/604.1',
            'X-CSRFToken: ' . $session['csrftoken'],
            'Cookie: csrftoken=' . $session['csrftoken'] . '; sessionid=' . $session['sessionid']
        );

        if ($proxy) {
            $post = curl($url, 0, $header, $session['username'], $proxy, $proxyauth);
        } else {
            $post = curl($url, 0, $header, $session['username']);
        }
        if (strpos($post, 'Location: https://www.instagram.com/accounts/login/') && strpos($post, '302 Found')) {

            $data = array(
                'status' => 'error',
                'details' => 'you are note logged in'
            );
        } elseif (strpos($post, 'Location: https://www.instagram.com/' . $username) && strpos($post, '302 Found')) {

            $data = array(
                'username' => $username,
                'action' => 'follow',
                'status' => 'success',
            );
        } elseif ($username['is_follow'] == 'true') {

            $data = array(
                'username' => $username,
                'action' => 'follow',
                'status' => 'error',
                'details' => 'already follow',
            );
        } else {

            $data = array(
                'username' => $username,
                'action' => 'follow',
                'status' => 'error',
                'details' => 'unexpected error',
            );
        }
    }

    return $data;
}

function block($username, $session, $proxy = 0, $proxyauth = 0)
{
    if ($proxy) {
        $profile = findProfile($username, $session, $proxy, $proxyauth);
    } else {
        $profile = findProfile($username, $session);
    }
    if (isset($profile['id'])) {
        $id = $profile['id'];
        $url = 'https://www.instagram.com/web/friendships/' . $id . '/block/';
        $header = array(
            'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 11_0_1 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A402 Safari/604.1',
            'X-CSRFToken: ' . $session['csrftoken'],
            'Cookie: csrftoken=' . $session['csrftoken'] . '; sessionid=' . $session['sessionid']
        );

        if ($proxy) {
            $post = curl($url, 0, $header, $session['username'], $proxy, $proxyauth);
        } else {
            $post = curl($url, 0, $header, $session['username']);
        }
        $data = (strpos($post, '{"status": "ok"}')) ? array("username_target" => $username, "action" => "block", "status" => "success") : array("username_target" => $username, "action" => "block", "status" => "error");
    } else {

        $data = array("username" => $username, "status" => "error", "details" => "username not found");
    }

    return $data;
}

function unblock($username, $session, $proxy = 0, $proxyauth = 0)
{
    if ($proxy) {
        $profile = findProfile($username, $session, $proxy, $proxyauth);
    } else {
        $profile = findProfile($username, $session);
    }
    if (isset($profile['id'])) {
        $id = $profile['id'];
        $url = 'https://www.instagram.com/web/friendships/' . $id . '/unblock/';
        $header = array(
            'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 11_0_1 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A402 Safari/604.1',
            'X-CSRFToken: ' . $session['csrftoken'],
            'Cookie: csrftoken=' . $session['csrftoken'] . '; sessionid=' . $session['sessionid']
        );

        if ($proxy) {
            $post = curl($url, 0, $header, $session['username'], $proxy, $proxyauth);
        } else {
            $post = curl($url, 0, $header, $session['username']);
        }
        $data = (strpos($post, '{"status": "ok"}')) ? array("username_target" => $username, "action" => "unblock", "status" => "success") : array("username_target" => $username, "action" => "unblock", "status" => "error");
    } else {

        $data = array("username" => $username, "status" => "error", "details" => "username not found");
    }

    return $data;
}

function deletePost($id, $session, $proxy = 0, $proxyauth = 0)
{
    $data = array();
    $url = 'https://www.instagram.com/create/' . $id . '/delete/';
    $header = array(
        'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 11_0_1 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A402 Safari/604.1',
        'X-CSRFToken: ' . $session['csrftoken'],
        'Cookie: csrftoken=' . $session['csrftoken'] . '; sessionid=' . $session['sessionid']
    );
    if ($proxy) {
        $delete = curl($url, 0, $header, $session['username'], $proxy, $proxyauth);
    } else {
        $delete = curl($url, 0, $header, $session['username']);
    }
    if (strpos($delete, 'This page could not be loaded.')) {

        $data['status'] = 'error';
        $data['details'] = 'make sure you already login';
    } elseif (strpos($delete, '{"did_delete": true, "status": "ok"}')) {

        $data['status'] = 'success';
        $data['action'] = 'delete';
        $data['id'] = $id;
    } else {

        $data['status'] = 'error';
        $data['details'] = 'unexpected error, please contact Admin';
    }

    return $data;
}

function getPassword($prompt = "[?] Enter Password: ")
{

    echo $prompt;
    $password = trim(fgets(STDIN));

    return $password;
}

function getUsername($prompt = "[?] Enter Username: ")
{
    echo $prompt;

    $username = trim(fgets(STDIN));

    return $username;
}

function getComment($prompt = "[?] Enter Comment Text: ")
{
    echo $prompt;

    $text = trim(fgets(STDIN));

    return $text;
}

function getFollowingLink($username, $count, $after = '', $session, $proxy = 0, $proxyauth = 0)
{

    if ($proxy) {
        $profile = findProfile($username, $session, $proxy, $proxyauth);
    } else {
        $profile = findProfile($username, $session);
    }
    if (isset($profile['id'])) {

        $accountId = $profile['id'];
        $url_folls = 'https://www.instagram.com/graphql/query/?query_id=17874545323001329&id={{accountId}}&first={{count}}&after={{after}}';
        $url = str_replace('{{accountId}}', urlencode($accountId), $url_folls);
        $url = str_replace('{{count}}', urlencode($count), $url);
        if ($after === '') {
            $url = str_replace('&after={{after}}', '', $url);
        } else {
            $url = str_replace('{{after}}', urlencode($after), $url);
        }
    } else {

        $url = '';
    }

    return $url;
}

function getMediaIdLink($id, $count, $after = '')
{
    if (isset($id)) {
        $url_media = 'https://www.instagram.com/graphql/query/?query_hash=eddbde960fed6bde675388aac39a3657&variables={"id":"{{id}}","first":"{{count}}","after":"{{after}}"}';
        $url = str_replace('{{id}}', $id, $url_media);
        $url = str_replace('{{count}}', $count, $url);
        if ($after === '') {
            $url = str_replace(',"after":"{{after}}"', '', $url);
        } else {
            $url = str_replace('{{after}}', $after, $url);
        }
    } else {
        $url = '';
    }
    return $url;
}
function getMediaId($id, $session, $count = 20, $pageSize = 20, $proxy = 0, $proxyauth = 0)
{
    $index = 0;
    $accounts = [];
    $endCursor = '';
    $header = array();
    $header[] = 'Cookie: sessionid=' . $session['sessionid'] . ';';
    $header[] = 'X-Csrftoken: ' . $session['csrftoken'] . ';';
    if ($count < $pageSize) {

        echo 'Count must be greater than or equal to page size.' . PHP_EOL;
    }
    while (true) {

        $url = getMediaIdLink($id, 20, $endCursor);
        if ($url != '') {
            if ($proxy) {
                $response = curlNoHeader($url, 0, $header, $session['username'], $proxy, $proxyauth);
            } else {
                $response = curlNoHeader($url, 0, $header);
            }
        }
        $data = json_decode($response);
        $pageInfo = $data->data->user->edge_owner_to_timeline_media;

        if ($pageInfo->count == 0) {
            $data = array(
                'status' => 'error'
            );
            return $data;
        }
        if (count($pageInfo->edges) == 0) {
            $data = array(
                'status' => 'error'
            );
            return $data;
        }

        foreach ($pageInfo->edges as $edge) {
            $accounts[] = $edge->node;
            $index++;
            if ($index >= $count) {
                break 2;
            }
        }

        if ($pageInfo->page_info->has_next_page) {

            $endCursor = $pageInfo->page_info->end_cursor;
        } else {

            $endCursor = '';
            break;
        }
    }

    return $accounts;
}


function getMediaLink($shortcode, $count, $after = '')
{
    if (isset($shortcode)) {
        if ($after === '') {
            $body = urlencode('{"shortcode":"' . $shortcode . '","include_reel":true,"first":"' . $count . '"}');
        } else {
            $body = urlencode('{"shortcode":"' . $shortcode . '","include_reel":true,"first":"' . $count . '","after":"' . $after . '"}');
        }
        $url = 'https://www.instagram.com/graphql/query/?query_hash=d5d763b1e2acf209d62d22d184488e57&variables=' . $body . '';
    } else {
        $url = '';
    }
    return $url;
}

function getMediaLikers($username, $session, $count = 20, $pageSize = 20, $proxy = 0, $proxyauth = 0)
{
    $index = 0;
    $accounts = [];
    $endCursor = '';
    $header = array();
    $header[] = 'Cookie: sessionid=' . $session['sessionid'] . ';';
    $header[] = 'X-Csrftoken: ' . $session['csrftoken'] . ';';
    if ($count < $pageSize) {

        echo 'Count must be greater than or equal to page size.' . PHP_EOL;
    }
    while (true) {

        $url = getMediaLink($username, 50, $endCursor);

        if ($url != '') {
            if ($proxy) {
                $response = curlNoHeader($url, 0, $header, $session['username'], $proxy, $proxyauth);
            } else {
                $response = curlNoHeader($url, 0, $header, $session['username']);
            }
        }

        $data = json_decode($response);
        $pageInfo = $data->data->shortcode_media->edge_liked_by;

        if ($pageInfo->count == 0) {

            return $accounts;
        }
        if (count($pageInfo->edges) == 0) {

            echo 'Failed to get followers of ' . $username . '. The account is private.';
        }

        foreach ($pageInfo->edges as $edge) {
            $accounts[] = $edge->node;
            $index++;
            if ($index >= $count) {
                break 2;
            }
        }

        if ($pageInfo->page_info->has_next_page) {

            $endCursor = $pageInfo->page_info->end_cursor;
        } else {

            $endCursor = '';
            break;
        }
    }

    return $accounts;
}

function getFollowersLink($username, $count, $after = '', $session, $proxy = 0, $proxyauth = 0)
{
    if (isset($username)) {

        $accountId = $username;
        $url_folls = 'https://www.instagram.com/graphql/query/?query_id=17851374694183129&id={{accountId}}&first={{count}}&after={{after}}';
        $url = str_replace('{{accountId}}', urlencode($accountId), $url_folls);
        $url = str_replace('{{count}}', urlencode($count), $url);
        if ($after === '') {
            $url = str_replace('&after={{after}}', '', $url);
        } else {
            $url = str_replace('{{after}}', urlencode($after), $url);
        }
    } else {

        $url = '';
    }

    return $url;
}

function getFollowers($username, $session, $count = 20, $pageSize = 20, $proxy = 0, $proxyauth = 0)
{
    $index = 0;
    $accounts = [];
    $endCursor = '';
    $header = array();
    $header[] = 'Cookie: sessionid=' . $session['sessionid'] . ';';
    if ($count < $pageSize) {

        echo 'Count must be greater than or equal to page size.' . PHP_EOL;
    }
    while (true) {
        if ($proxy) {
            $url = getFollowersLink($username, 100, $endCursor, $session, $proxy, $proxyauth);
        } else {
            $url = getFollowersLink($username, 100, $endCursor, $session);
        }

        if ($url != '') {
            if ($proxy) {
                $response = curlNoHeader($url, 0, $header, $session['username'], $proxy, $proxyauth);
            } else {
                $response = curlNoHeader($url, 0, $header, $session['username']);
            }
        }

        $data = json_decode($response);
        $pageInfo = $data->data->user->edge_followed_by;

        if ($pageInfo->count == 0) {

            return $accounts;
        }
        if ($pageInfo->edges == 0) {

            echo 'Failed to get followers of ' . $username . '. The account is private.';
        }

        foreach ($pageInfo->edges as $edge) {
            $accounts[] = $edge->node;
            $index++;
            if ($index >= $count) {
                break 2;
            }
        }

        if ($pageInfo->page_info->has_next_page) {

            $endCursor = $pageInfo->page_info->end_cursor;
        } else {

            $endCursor = '';
            break;
        }
    }

    return $accounts;
}

function getFollowing($username, $session, $count = 20, $pageSize = 20, $proxy = 0, $proxyauth = 0)
{
    $index = 0;
    $accounts = [];
    $endCursor = '';
    $header = array();
    $header[] = 'Cookie: sessionid=' . $session['sessionid'] . ';';
    if ($count < $pageSize) {

        echo 'Count must be greater than or equal to page size.' . PHP_EOL;
    }
    while (true) {
        if ($proxy) {
            $url = getFollowingLink($username, 100, $endCursor, $session, $proxy, $proxyauth);
        } else {
            $url = getFollowingLink($username, 100, $endCursor, $session);
        }

        if ($url != '') {
            if ($proxy) {
                $response = curlNoHeader($url, 0, $header, $session['username'], $proxy, $proxyauth);
            } else {
                $response = curlNoHeader($url, 0, $header, $session['username']);
            }
        }
        $data = json_decode($response);
        $pageInfo = $data->data->user->edge_follow;

        if ($pageInfo->count == 0) {

            return $accounts;
        }
        if ($pageInfo->edges == 0) {

            return 'Failed to get followers of ' . $username . '. The account is private.';
        }

        foreach ($pageInfo->edges as $edge) {
            $accounts[] = $edge->node;
            $index++;
            if ($index >= $count) {
                break 2;
            }
        }

        if ($pageInfo->page_info->has_next_page) {

            $endCursor = $pageInfo->page_info->end_cursor;
        } else {

            $endCursor = '';
            break;
        }
    }

    return $accounts;
}

function getAccountMediasJsonLink($variables)
{
    return str_replace('{variables}', urlencode($variables), 'https://www.instagram.com/graphql/query/?query_hash=42323d64886122307be10013ad2dcc44&variables={variables}');
}

function getMedias($username, $session, $count = 0, $maxId = '', $proxy = 0, $proxyauth = 0)
{
    if ($proxy) {
        $profile = findProfile($username, $session, $proxy, $proxyauth);
    } else {
        $profile = findProfile($username, $session);
    }
    $id = $profile['id'];
    $index = 0;
    $medias = [];
    $isMoreAvailable = true;
    $header = array();
    $header[] = 'Cookie: sessionid=' . $session['sessionid'] . ';';
    while ($index < $count && $isMoreAvailable) {
        $variables = json_encode([
            'id' => (string)$id,
            'first' => (string)$count,
            'after' => (string)$maxId
        ]);

        $url = getAccountMediasJsonLink($variables);

        if ($proxy) {
            $response = curlNoHeader($url, 0, $header, $session['username'], $proxy, $proxyauth);
        } else {
            $response = curlNoHeader($url, 0, $header, $session['username']);
        }

        $obj = json_decode($response);
        $nodes = $obj->data->user->edge_owner_to_timeline_media->edges;
        // fix - count takes longer/has more overhead
        if (!isset($nodes) || empty($nodes)) {
            return [];
        }

        foreach ($nodes as $mediaObject) {
            if ($index === $count) {
                return $medias;
            }
            $medias[] = $mediaObject->node;
            $index++;
        }
        if (empty($nodes) || !isset($nodes)) {
            return $medias;
        }
        $maxId = $obj->data->user->edge_owner_to_timeline_media->page_info->end_cursor;
        $isMoreAvailable = $obj->data->user->edge_owner_to_timeline_media->page_info->has_next_page;
    }
    return $medias;
}

function getMedia($username, $login, $no, $maxId = '', $proxy = 0, $proxyauth = 0)
{
    if ($proxy) {
        $media = getMedias($username, $login, $no, $maxId, $proxy, $proxyauth);
    } else {
        $media = getMedias($username, $login, $no);
    }
    $dataObj = $media[$no - 1];
    if ($dataObj) {

        $vid = ($dataObj->is_video == 1) ? "yes" : "no";
        $data = array(
            'status' => 'success',
            'id' => $dataObj->id,
            'page' => 'https://instagram.com/p/' . $dataObj->shortcode,
            'comments' => $dataObj->edge_media_to_comment->count,
            'likes' => $dataObj->edge_media_preview_like->count,
            'url_display' => $dataObj->display_url,
            'is_video' => $vid
        );
    } else {

        $data = array(
            'status' => 'error',
            'details' => 'Media Not Found.'
        );
    }

    return $data;
}
function getPostnew($username, $session = 0, $no = 0, $proxy = 0, $proxyauth = 0)
{
    $url = 'https://instagram.com/' . $username;
    if (!$session == 0) {
        $header = array();
        $header[] = 'Cookie: sessionid=' . $session['sessionid'] . ';';
        if ($proxy) {
            $get = curl($url, 0, $header, $session['username'], $proxy, $proxyauth);
        } else {
            $get = curl($url, 0, $header, $session['username']);
        }
    } else {

        $get = curl($url);
    }

    if (strpos($get, 'The link you followed may be broken, or the page may have been removed.')) {

        $data = array(
            'status' => 'error',
            'details' => 'user not found'
        );
    } else {

        $data_ouput = getStr($get, '<script type="text/javascript">window._sharedData = ', ';</script>');
        $data_array = json_decode($data_ouput);
        $result = $data_array->entry_data->ProfilePage['0']->graphql->user->edge_owner_to_timeline_media->edges;
        if (empty($result) && $data_array->entry_data->ProfilePage['0']->graphql->user->edge_owner_to_timeline_media->count >= 1) {

            $result = $data_array->entry_data->ProfilePage['0']->graphql->user;
            $data = array(
                'status' => 'error',
                'details' => 'account private',
                'id' => $result->id,
                'username' => $username,
                'fullname' => $result->full_name,
                'followers' => $result->edge_followed_by->count,
                'following' => $result->edge_follow->count,
                'post' => $result->edge_owner_to_timeline_media->count,
            );
        } elseif ($data_array->entry_data->ProfilePage['0']->graphql->user->edge_owner_to_timeline_media->count <= 0) {

            $result = $data_array->entry_data->ProfilePage['0']->graphql->user;
            $data = array(
                'status' => 'error',
                'details' => 'no post',
                'id' => $result->id,
                'username' => $username,
                'fullname' => $result->full_name,
                'followers' => $result->edge_followed_by->count,
                'following' => $result->edge_follow->count,
                'post' => $result->edge_owner_to_timeline_media->count,
            );
        } else {
            $result = $data_array->entry_data->ProfilePage['0']->graphql->user;
            $result = $data_array->entry_data->ProfilePage['0']->graphql->user->edge_owner_to_timeline_media->edges['0']->node;
            $vid = ($result->is_video == 1) ? "yes" : "no";

            $data = array(
                'status' => 'success',
                'id' => $result->id,
                'page' => 'https://instagram.com/p/' . $result->shortcode,
                'comments' => $result->edge_media_to_comment->count,
                'likes' => $result->edge_liked_by->count,
                'url_display' => $result->display_url,
                'is_video' => $vid
            );
        }
    }

    return $data;
}

function getPost($username, $session = 0, $no = 0, $proxy = 0, $proxyauth = 0)
{
    $url = 'https://instagram.com/' . $username;
    if ($session != 0) {
        $header = array();
        $header[] = 'Cookie: sessionid=' . $session['sessionid'] . ';';
        if ($proxy) {
            $get = curl($url, 0, $header, $session['username'], $proxy, $proxyauth);
            // $ip = curl('https://api64.ipify.org/?format=json', 0, 0, 0, $proxy, $proxyauth);
            // var_dump($ip);
        } else {
            $get = curl($url, 0, $header, $session['username']);
        }
    } else {
        if ($proxy) {
            $get = curl($url, 0, 0, $session['username'], $proxy, $proxyauth);
        } else {
            $get = curl($url);
        }
    }
    var_dump($get);
    if (strpos($get, 'The link you followed may be broken, or the page may have been removed.')) {

        $data = array(
            'status' => 'error',
            'details' => 'user not found'
        );
    } else {

        $data_ouput = getStr($get, '<script type="text/javascript">window._sharedData = ', ';</script>');
        $data_array = json_decode($data_ouput);
        $result = $data_array->entry_data->ProfilePage['0']->graphql->user->edge_owner_to_timeline_media->edges;
        if (empty($result) && $data_array->entry_data->ProfilePage['0']->graphql->user->edge_owner_to_timeline_media->count >= 1) {

            $result = $data_array->entry_data->ProfilePage['0']->graphql->user;
            $data = array(
                'status' => 'error',
                'details' => 'account private',
                'id' => $result->id,
                'username' => $username,
                'fullname' => $result->full_name,
                'followers' => $result->edge_followed_by->count,
                'following' => $result->edge_follow->count,
                'post' => $result->edge_owner_to_timeline_media->count,
            );
        } elseif ($data_array->entry_data->ProfilePage['0']->graphql->user->edge_owner_to_timeline_media->count <= 0) {

            $result = $data_array->entry_data->ProfilePage['0']->graphql->user;
            $data = array(
                'status' => 'error',
                'details' => 'no post',
                'id' => $result->id,
                'username' => $username,
                'fullname' => $result->full_name,
                'followers' => $result->edge_followed_by->count,
                'following' => $result->edge_follow->count,
                'post' => $result->edge_owner_to_timeline_media->count,
            );
        } else {
            $result = $data_array->entry_data->ProfilePage['0']->graphql->user;
            $result = $data_array->entry_data->ProfilePage['0']->graphql->user->edge_owner_to_timeline_media->edges['0']->node;
            $vid = ($result->is_video == 1) ? "yes" : "no";

            $data = array(
                'status' => 'success',
                'id' => $result->id,
                'page' => 'https://instagram.com/p/' . $result->shortcode,
                'comments' => $result->edge_media_to_comment->count,
                'likes' => $result->edge_liked_by->count,
                'url_display' => $result->display_url,
                'is_video' => $vid
            );
        }
    }

    return $data;
}

function activity($session, $proxy = 0, $proxyauth = 0)
{
    $url = 'https://www.instagram.com/accounts/activity/';
    $header = array(
        'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 11_0_1 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A402 Safari/604.1',
        'X-CSRFToken: ' . $session['csrftoken'],
        'Cookie: csrftoken=' . $session['csrftoken'] . '; sessionid=' . $session['sessionid']
    );
    if ($proxy) {
        $post = curl($url, 0, $header, $session['username'], $proxy, $proxyauth);
    } else {
        $post = curl($url, 0, $header, $session['username']);
    }
    $data_ouput = getStr($post, '<script type="text/javascript">window._sharedData = ', ';</script>');
    $data_array = json_decode($data_ouput);
    $result = $data_array->entry_data->ActivityFeed['0']->graphql->user->activity_feed->edge_web_activity_feed;
    foreach ($result->edges as $items) {
        $data[] = array(
            'status' => 'success',
            'username' => $items->node->user->username,
            'type' => $items->node->type,
            'text' => $items->node->text,
        );
    }
    return $data;
}

function getHome($session, $proxy = 0, $proxyauth = 0)
{
    $url = 'https://www.instagram.com/';
    $header = array(
        'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 11_0_1 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A402 Safari/604.1',
        'X-CSRFToken: ' . $session['csrftoken'],
        'Cookie: csrftoken=' . $session['csrftoken'] . '; sessionid=' . $session['sessionid']
    );
    if ($proxy) {
        $post = curl($url, 0, $header, $session['username'], $proxy, $proxyauth);
    } else {
        $post = curl($url, 0, $header, $session['username']);
    }
    $data_ouput = getStr($post, "window.__additionalDataLoaded('feed',", ");</script>");
    $data_array = json_decode($data_ouput);
    $result = $data_array->user->edge_web_feed_timeline;
    foreach ($result->edges as $items) {
        $data[] = array(
            'status' => 'success',
            'username' => $items->node->owner->username,
            'id' => $items->node->id,
            'link' => 'https://instagram.com/p/' . $items->node->shortcode,
            'text' => $items->node->edge_media_to_caption->edges[0]->node->text
        );
    }
    return $data_ouput;
}

function onetap($session, $proxy = 0, $proxyauth = 0)
{
    $url = 'https://www.instagram.com/accounts/onetap/?next=%2F';
    $header = array(
        'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 11_0_1 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A402 Safari/604.1',
        'X-CSRFToken: ' . $session['csrftoken'],
        'Cookie: csrftoken=' . $session['csrftoken'] . '; sessionid=' . $session['sessionid']
    );
    if ($proxy) {
        $post = curl($url, 0, $header, $session['username'], $proxy, $proxyauth);
    } else {
        $post = curl($url, 0, $header, $session['username']);
    }
    $data_ouput = getStr($post, '<script type="text/javascript">window._sharedData = ', ';</script>');
    $data_array = json_decode($data_ouput);
    $result = $data_array->config->viewer;
    $data = array(
        'status' => 'success',
        'name' => $result->full_name,
        'username' => $result->username,
    );
    return $data;
}
