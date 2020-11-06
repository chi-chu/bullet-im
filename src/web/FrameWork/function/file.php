<?php

function file_fetch($url, $limit = 0, $path = '') {
    global $_W;
    $url = trim($url);
    if(empty($url)) {
        return error(-1, '文件地址不存在');
    }
    if(!$limit) {
        $limit = $_W['setting']['upload']['image']['limit'] * 1024;
    } else {
        $limit = $limit * 1024;
    }
    if(empty($path)) {
        $path =  "images/{$_W['uniacid']}/" . date('Y/m/');
    }
    if(!file_exists(APP_ROOT . $path)) {
        mkdirs(APP_ROOT . $path);
    }
    $resp = ihttp_get($url);
    if (is_error($resp)) {
        return error(-1, '提取文件失败, 错误信息: '.$resp['message']);
    }
    if (intval($resp['code']) != 200) {
        return error(-1, '提取文件失败: 未找到该资源文件.');
    }
    $ext = '';
    switch ($resp['headers']['Content-Type']){
        case 'application/x-jpg':
        case 'image/jpeg':
            $ext = 'jpg';
            break;
        case 'image/png':
            $ext = 'png';
            break;
        case 'image/gif':
            $ext = 'gif';
            break;
        default:
            return error(-1, '提取资源失败, 资源文件类型错误.');
            break;
    }

    if (intval($resp['headers']['Content-Length']) > $limit) {
        return error(-1, '上传的媒体文件过大('.sizecount($resp['headers']['Content-Length']).' > '.sizecount($limit));
    }
    $filename = file_random_name(APP_ROOT . $path, $ext);
    $pathname = $path . $filename;
    $fullname = APP_ROOT . $pathname;
    if (file_put_contents($fullname, $resp['content']) == false) {
        return error(-1, '提取失败.');
    }
    return $pathname;
}

function file_image_thumb($srcfile, $desfile = '', $width = 0) {
    global $_W;

    if (!file_exists($srcfile)) {
        return error('-1', '原图像不存在');
    }
    if (intval($width) == 0) {
        load()->model('setting');
        $width = intval($_W['setting']['upload']['image']['width']);
    }
    if (intval($width) < 0) {
        return error('-1', '缩放宽度无效');
    }

    if (empty($desfile)) {
        $ext = pathinfo($srcfile, PATHINFO_EXTENSION);
        $srcdir = dirname($srcfile);
        do {
            $desfile = $srcdir . '/' . random(30) . ".{$ext}";
        } while (file_exists($desfile));
    }

    $des = dirname($desfile);
    if (!file_exists($des)) {
        if (!mkdirs($des)) {
            return error('-1', '创建目录失败');
        }
    } elseif (!is_writable($des)) {
        return error('-1', '目录无法写入');
    }

    $org_info = @getimagesize($srcfile);
    if ($org_info) {
        if ($width == 0 || $width > $org_info[0]) {
            copy($srcfile, $desfile);
            return str_replace(ATTACHMENT_ROOT . '/', '', $desfile);
        }
        if ($org_info[2] == 1) { 			if (function_exists("imagecreatefromgif")) {
            $img_org = imagecreatefromgif($srcfile);
        }
        } elseif ($org_info[2] == 2) {
            if (function_exists("imagecreatefromjpeg")) {
                $img_org = imagecreatefromjpeg($srcfile);
            }
        } elseif ($org_info[2] == 3) {
            if (function_exists("imagecreatefrompng")) {
                $img_org = imagecreatefrompng($srcfile);
                imagesavealpha($img_org, true);
            }
        }
    } else {
        return error('-1', '获取原始图像信息失败');
    }
    $scale_org = $org_info[0] / $org_info[1];
    $height = $width / $scale_org;
    if (function_exists("imagecreatetruecolor") && function_exists("imagecopyresampled") && @$img_dst = imagecreatetruecolor($width, $height)) {
        imagealphablending($img_dst, false);
        imagesavealpha($img_dst, true);
        imagecopyresampled($img_dst, $img_org, 0, 0, 0, 0, $width, $height, $org_info[0], $org_info[1]);
    } else {
        return error('-1', 'PHP环境不支持图片处理');
    }
    if ($org_info[2] == 2) {
        if (function_exists('imagejpeg')) {
            imagejpeg($img_dst, $desfile);
        }
    } else {
        if (function_exists('imagepng')) {
            imagepng($img_dst, $desfile);
        }
    }

    imagedestroy($img_dst);
    imagedestroy($img_org);

    return str_replace(APP_ROOT . '/', '', $desfile);
}