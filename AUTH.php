<?php
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
    $cookie = CheckLiveCookie($results);
    echo '[•] Check Live Cookie ' . $results['username'] . '' . PHP_EOL;
    if ($cookie['status'] == 'success') {
        Run($results);
    }
} else {
    $u = getUsername();
    $p = getPassword();
    echo PHP_EOL;
    $results = ($proxy != '' && $proxyauth != '') ? logins($u, $p, $proxy, $proxyauth) : logins($u, $p);
    if ($results['status'] == 'success') {
        echo color()["LG"] . '[*] Login as ' . $results['username'] . ' Success!' . PHP_EOL;
        SaveData($results);
        Run($results);
    }
}
