<?php

function now() {
    return date('Y-m-d H:i:s');
}

function error($errno, $message = '') {
    return array(
        'errno' => $errno,
        'message' => $message,
    );
}

function is_error($data) {
    if (empty($data) || !is_array($data) || !array_key_exists('errno', $data) || (array_key_exists('errno', $data) && $data['errno'] == 0)) {
        return false;
    } else {
        return true;
    }
}

function sizecount($size) {
    if($size >= 1073741824) {
        $size = round($size / 1073741824 * 100) / 100 . ' GB';
    } elseif($size >= 1048576) {
        $size = round($size / 1048576 * 100) / 100 . ' MB';
    } elseif($size >= 1024) {
        $size = round($size / 1024 * 100) / 100 . ' KB';
    } else {
        $size = $size . ' Bytes';
    }
    return $size;
}

function array2xml($arr, $level = 1) {
    $s = $level == 1 ? "<xml>" : '';
    foreach ($arr as $tagname => $value) {
        if (is_numeric($tagname)) {
            $tagname = $value['TagName'];
            unset($value['TagName']);
        }
        if (!is_array($value)) {
            $s .= "<{$tagname}>" . (!is_numeric($value) ? '<![CDATA[' : '') . $value . (!is_numeric($value) ? ']]>' : '') . "</{$tagname}>";
        } else {
            $s .= "<{$tagname}>" . array2xml($value, $level + 1) . "</{$tagname}>";
        }
    }
    $s = preg_replace("/([\x01-\x08\x0b-\x0c\x0e-\x1f])+/", ' ', $s);
    return $level == 1 ? $s . "</xml>" : $s;
}

function parseRequest() {
    $ret = array('m' => 'index', 'c' => 'index', 'f' => 'index');
    $pre = '';
    foreach (explode('/', $_SERVER['REQUEST_URI']) as $k => $v) {
        if ($k == 0) {
            continue;
        } else if ($k == 1) {
            $ret['m'] = $v;
        } else if ($k == 2) {
            $ret['c'] = $v;
        } else if ($k == 3) {
            $ret['f'] = $v;
        } else {
            if ($pre == '') {
                $_GET[$v] = '';
                $pre = $v;
            } else {
                $_GET[$pre] = $v;
                $pre = '';
            }
        }
    }
    return $ret;
}

function G($name, $type = 1) {
    global $_load;
    if ($type == 1) {
        $_load->classes($name);
        return new $name;
    }
}

function C($name) {
    global $_load;
    $_load->c($name);
}

function M($name) {
    global $_load;
    $_load->c($name);
}

function E($name) {
    if (DIRECTORY_SEPARATOR == '/') {
        $name = str_replace('\\', '/', $name);
    }else {
        $name = str_replace('/', '\\', $name);
    }
    $a = explode(DIRECTORY_SEPARATOR, $name);
    $class = $a[count($a)-1];
    if(file_exists(APP_EXTEND.DIRECTORY_SEPARATOR.$name.'.php')) {
        require APP_EXTEND.DIRECTORY_SEPARATOR.$name.'.php';
    }
    return new $class;
}