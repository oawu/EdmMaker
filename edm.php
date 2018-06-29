<?php

// 圖片的資料夾
$imgPath = 'img/';

// 寬度
$width = 640;

// 超鏈結
$links = [
  '03.png' => [
    ['top' => 10, 'left' => 60, 'width' => 180, 'height' => 52, 'href' => 'https://www.google.com.tw/'],
  ]
];

// 是否顯示超鏈結
$isDebug = false;





// ======== 以下不要動 ========


// 函式
if (!function_exists('dirMap')) { function dirMap($srcDir, $dirDepth = 0, $hidden = false) { if ($fp = @opendir($srcDir)) { $filedata = []; $new_depth = $dirDepth - 1; $srcDir = rtrim($srcDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR; while (false !== ($file = readdir($fp))) { if ($file === '.' || $file === '..' || ($hidden === false && $file[0] === '.')) continue; is_dir($srcDir . $file) && $file .= DIRECTORY_SEPARATOR; if (($dirDepth < 1 || $new_depth > 0) && is_dir($srcDir . $file)) $filedata[$file] = dirMap ($srcDir . $file, $new_depth, $hidden); else $filedata[] = $file; } closedir($fp); return $filedata; } return false; } }
if (!function_exists('writeFile')) { function writeFile($path, $data, $mode = 'wb') { if (!$fp = @fopen($path, $mode)) return false; flock($fp, LOCK_EX); for ($result = $written = 0, $length = strlen($data); $written < $length; $written += $result) if (($result = fwrite($fp, substr($data, $written))) === false) break; flock($fp, LOCK_UN); fclose($fp); return is_int($result); } }
if (!function_exists('dimension')) { function dimension($file) { $img = null; switch (pathinfo($file, PATHINFO_EXTENSION)) { case 'gif': $img = imagecreatefromgif($file); break; case 'jpg': $img = imagecreatefromjpeg($file); break; case 'png': $img = imagecreatefrompng($file); break; } return $img ? [imagesx($img), imagesy($img)] : []; } }

// 格式
$imgPath = rtrim($imgPath, '/') . '/';
$links = array_combine(array_map(function($key) use($imgPath) { return $imgPath . $key; }, array_keys($links)), $links);
foreach ($links as $key => $link) foreach ($link as $attr) isset($attr['top'], $attr['left'], $attr['width'], $attr['height'], $attr['href']) || exit("超鏈結格式有誤，請檢查是否每個鏈結都有設定 top、left、width、height、href！");

// 撈圖
$imgs = array_map(function($img) use($imgPath) { return $imgPath . $img; }, array_filter(dirMap($imgPath), function($img) { return in_array(pathinfo($img, PATHINFO_EXTENSION), ['png', 'jpg', 'gif']); }));
asort($imgs);

// 每列格式
$trs = array_filter(array_map(function ($img) use($width, $links, $isDebug) {
  if (!$dimension = dimension($img))
    return '';

  $hasLinks = isset($links[$img]);
  $tr  = '';
  $tr .= str_repeat(' ', 8)  . '<!-- 圖片 ' . $img . ' -->' . "\n";
  $tr .= str_repeat(' ', 8)  . '<tr><td style="position: relative;">' . "\n";
  $tr .= str_repeat(' ', 10) . '<a href="#" style="pointer-events:none;"><img class="g-img" src="' . $img . '" width="' . $width . '" height="' . $dimension[1] . '" border="0" alt="" style="display:block"></a>' . "\n";
  $tr .= $hasLinks ? "\n" . str_repeat(' ', 10) . '<!-- ' . $img . ' 區塊的 超鏈結 -->' . "\n" . implode('', array_map(function($link) use($isDebug) {
    
    $style = array_merge(['position' => 'absolute'], $link);
    $isDebug && $style = array_merge($style, ['border' => '1px solid #fff', 'color' => '#fff', 'line-height' => $style['height'], 'background-color' => 'rgba(0,0,0,0.5)']);
    foreach ($style as $key => &$val) in_array($key, ['top', 'left', 'width', 'height', 'line-height']) && $val .= 'px';
    $href = $style['href'];
    unset($style['href']);
    $style = implode(';', array_map(function ($key, $val) { return $key . ':' . $val; }, array_keys($style), $style));

    return str_repeat(' ', 10) . '<a href="' . $href . '" style="' . $style . '">' . ($isDebug ? '超鏈結' : '') . '</a>' . "\n";
  }, $links[$img])) : '';
  $tr .= str_repeat(' ', 8)  . '</td></tr>' . "\n";

  return $tr;
}, $imgs));

// 網站格式
$html  = '';
$html .= '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">' . "\n";
$html .= '<html>' . "\n";
$html .= '  <head>' . "\n";
$html .= '    <title></title>' . "\n";
$html .= '    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . "\n";
$html .= '    <style>img.g-img + div {display:none !important;}</style>' . "\n";
$html .= '  </head>' . "\n";

$html .= '  <body style="background-color: rgba(255, 255, 255, 1.00);">' . "\n";
$html .= '    <div style="display: inline-block; width: 100%; text-align: center;">' . "\n";
$html .= '      <table style="display: inline-block; max-width: ' . $width . 'px; width: 100%; position: relative;" width="640"  border="0" cellspacing="0" cellpadding="0">' . "\n\n";
$html .= implode("\n", array_map(function ($tr) { return $tr; }, $trs)) . "\n";
$html .= '      </table>' . "\n";
$html .= '    </div>' . "\n";
$html .= '  </body>' . "\n";
$html .= '</html>';

writeFile('mail.html', $html);

echo "搞定，請點開 mail.html 來看！";