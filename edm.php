<?php

// 圖片
// $layout = [
//   ['01.png'],
//   ['02.png', '03.png', '04.png'],
// ];
$layout = [
];

// 超鏈結
// $links = [
//   '03.png' => 'https://www.google.com.tw/',
// ];
$links = [
];

// 圖片資料夾
define('IMGDIR', 'img');

// 是否壓縮 html
define('ISMINI', true);

// 是否檢視超鏈結是否正常
define('DEBUG', false);

// 信件內容文字
define('CONTENT', "");

// 寬度
define('WIDTH', "");

// 圖片加入網址
define('BASEURL', '');

// 圖片使用 # 超鏈結
define('IMGLINK', false);

// 換行
define('LN', ISMINI ? '' : "\n");


// 以下別亂改

if (!function_exists('s')) { function s($n) { return ISMINI ? '' : str_repeat(' ', $n); } }
if (!function_exists('p')) { function p(&$h, $n, $t) { $h[] = s($n) . $t; } }
if (!function_exists('w')) { function w($p, $d, $m = 'wb') { if (!$fp = @fopen($p, $m)) return false; flock($fp, LOCK_EX); for ($result = $written = 0, $length = strlen($d); $written < $length; $written += $result) if (($result = fwrite($fp, substr($d, $written))) === false) break; flock($fp, LOCK_UN); fclose($fp); return is_int($result); } }
if (!function_exists('d')) { function d($f) { $i = null; switch (pathinfo($f, PATHINFO_EXTENSION)) { case 'gif': $i = imagecreatefromgif($f); break; case 'jpg': $i = imagecreatefromjpeg($f); break; case 'png': $i = imagecreatefrompng($f); break; } return $i ? [imagesx($i), imagesy($i)] : []; } }
if (!function_exists('t')) { function t($t) { return '<td style="width:' . $t[1] . 'px;height:' . $t[2] . 'px;vertical-align:top;">' . ($t[3] || IMGLINK ? '<a href="' . ($t[3] ? $t[3] : '#') . '" style="' . (!$t[3] ? 'pointer-events:none;cursor:auto;' : 'text-decoration:none;cursor:pointer;') . '">' : '') . (DEBUG && $t[3] ? '<div style="width:' . $t[1] . 'px;height:' . $t[2] . 'px;line-height:' . $t[2] . 'px;">' . $t[4] . '</div>' : '<img class="g-img" src="' . $t[0] . '" width="' . $t[1] . '" height="' . $t[2] . '" border="0" style="display:block">') . ($t[3] || IMGLINK ? '</a>' : '') . '</td>'; } }

define('DIR', rtrim(IMGDIR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR);
define('URL', rtrim(trim(BASEURL), '/') ? rtrim(trim(BASEURL), '/') . '/' : '');

$layout = array_values(array_filter(array_map(function($tr) use($links) { return array_values(array_filter(array_map(function($td) use($links) { return is_readable(DIR . $td) && ($d = d(DIR . $td)) ? [(URL ? URL : DIR) . $td, $d[0], $d[1], isset($links[$td]) ? $links[$td] : '', $td] : []; }, $tr))); }, $layout)));

define('WID', WIDTH ? WIDTH : array_sum(array_map(function ($t) { return $t[1]; }, $layout[0])));

// 整理 每列資訊
$trs = array_values(array_filter(array_map(function($tds) {
  $tr = [];
  if (count($tds) == 1) {
    $tr = [t($tds[0])];
  } else {
    p($tr, 0, '<td><table style="width:' . WID . 'px;" width="640"  border="0" cellspacing="0" cellpadding="0"><tbody><tr>');
    p($tr, 0, implode(LN, array_map(function($td) { return s(12) . t($td); }, $tds)));
    p($tr, 10, '</tr></tbody></table></td>');
  }

  return $tr ? s(10) . '<tr>' . implode(LN, $tr) . '</tr>' : '';
}, $layout)));

// 網站格式
$html = [];
p($html, 0, '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">');
p($html, 0, '<html>');
p($html, 2, '<head>');
p($html, 4, '<title></title>');
p($html, 4, '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">');
p($html, 4, '<style>img.g-img + div {display:none !important;}</style>');
p($html, 2, '</head>');
p($html, 2, '<body style="background-color: rgba(255, 255, 255, 1.00);text-align: center;">');
p($html, 4, '<div style="display: inline-block; width:' . WID . 'px;margin: 0 auto;">');
p($html, 6, '<table style="width:' . WID . 'px;" width="640"  border="0" cellspacing="0" cellpadding="0">');
p($html, 8, '<tbody>');
p($html, 0, implode(LN, array_map(function($tr) { return $tr; }, $trs)));
p($html, 8, '</tbody>');
p($html, 6, '</table>');
p($html, 4, '</div>');
CONTENT && p($html, 4, '<div style="max-width:0;max-height:0;overflow:hidden;">' . CONTENT . '</div>');
p($html, 2, '</body>');
p($html, 0, '</html>');

w('mail.html', implode(LN, $html));

echo "\n ➜ 已經成功產出 mail.html 檔案囉！\n\n ➜ 檔案位置：" . __dir__ . DIRECTORY_SEPARATOR . 'mail.html' . "\n\n";