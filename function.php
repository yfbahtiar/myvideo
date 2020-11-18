<?php
// base url
function base_url($link)
{
    $url = 'http://localhost/myvideo/';
    if ($link) {
        $toUrl = $url . $link;
        return $toUrl;
    } else {
        return $url;
    }
}

// koneksi db
$conn = mysqli_connect("localhost", "root", "", "myvideo");

// generate link dg acak huruf
function generateVideoLink($panjang)
{
    // tentukan karakter random
    $karakter = 'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz1234567890';
    // buat variabel kosong untuke kembalian nilai retur
    $string = '';
    // lakukan proses looping 
    for ($i = 0; $i < $panjang; $i++) {
        // lakukan random data dengan nilai awal 0 dan nilai akhir sebanyak value 'karakter'
        $pos = rand(0, strlen($karakter) - 1);
        // melakukan penggabungan antara nilai karakter di kiri dengan nilai di kanan
        $string .= $karakter[$pos];
    }
    // kembalikan nilai dari function ke echo
    return $string;
}
