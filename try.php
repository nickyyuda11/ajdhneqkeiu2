<?php
include 'class_ig_new_reply.php';

$id = '181380634';
$text = 'test';
$id_comment = '17882531378073317';

// $url = 'https://www.instagram.com/web/search/topsearch/?query=' . $id . '';
$url_info = 'https://www.instagram.com/dr.tirta/?__a=1';

$u = 'summonerswar.hacks';
$p = 'ninjasaga2';

// $login = logins($u, $p);

// var_dump($login);

$data_login = array(
    'username' => 'summonerswar.hacks',
    'csrftoken' => 'q8lsSvQ69T4sLj5zkBUKGexhNbymdTLY',
    'sessionid' => '44831725761:sHYz2Lyb8A7Xy9:21',
    'ds_user_id' => '44831725761',
    'ig_did' => '2115865A-8AFF-4179-BFEF-CEDF5E93AC9A',
    'mid' => 'YCPlXwAEAAGlQVkhzgZsGbLc-5gg',
    'useragent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 12_3_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 Instagram 105.0.0.11.118 (iPhone11,8; iOS 12_3_1; en_US; en-US; scale=2.00; 828x1792; 165586599)'
);

$post = curlNoHeader($url_info, 0, $data_login);
$data_array = json_decode($post);
$result = $data_array->graphql->user->is_private;
var_dump($result);
// $result = $data_array->users['0'];

// $data = array(
//     'status' => 'success',
//     'id' => $result->user->pk,
//     'username' => $result->user->username,
// );
// var_dump($data);

// $comment = ($proxy != '' && $proxyauth != '') ? ReplyComment($id, $id_comment, $data_login, $text, $proxy, $proxyauth) : ReplyComment($id, $id_comment, $data_login, $text);
// foreach ($data as $id_comment) {
//     $id_comment = $id_comment->id;
//     var_dump($id_comment);
// }
// var_dump($comment);
