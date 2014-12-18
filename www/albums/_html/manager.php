<?php

error_reporting(0);

class Manager {
    public $canEditHeader = TRUE;
    public $haveAcess = true;
    private $_encodedUrl = "";

    private function _base64url_decode($base64url) {
        $base64 = strtr($base64url, '-_', '+/');
        $plain = base64_decode($base64);
        return ($plain);
    }

    private function _mkdir($dir)  {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, TRUE);
        }
    }

    private function _delete($element)
    {
        if (is_dir($element))
        {
            $items = array_diff(scandir($element),array('.','..'));
            foreach ($items as $item)
            {
                $this->_delete($element."/".$item);
            }
            rmdir($element);
        }
        elseif (file_exists($element))
        {
            unlink($element);
        }
    }

    private function _clean_data_root($dir) {
        $items = array_diff(scandir(DATA_ROOT.'info/'.$dir),array('.','..','_sfpg_dir'));
        foreach($items as $item) {
            if (!is_dir(GALLERY_ROOT.$dir.$item) and !file_exists(GALLERY_ROOT.$dir.$item)) {
                $this->_delete(DATA_ROOT.'info/'.$dir.$item);
                $this->_delete(DATA_ROOT.'thumb/'.$dir.$item);
                $this->_delete(DATA_ROOT.'image/'.$dir.$item);
                $this->_delete(DATA_ROOT.'preview/'.$dir.$item);
            }
        }
        $path='';
        $dirs=explode('/',$dir);
        foreach($dirs as $dirout) {
            if(file_exists(DATA_ROOT.'info/'.$path.'_sfpg_dir')) {
                unlink(DATA_ROOT.'info/'.$path.'_sfpg_dir');
            }
            $path.=$dirout.'/';
        }
    }

    private function _dir_info($dir, $initial=TRUE) {
        $manager = new Manager();
        list($dirs, $images, $files, $misc) = $manager->get_dir($dir);
        if ($initial) {
            $info = count($dirs).'|'.count($images).'|'.count($files).'|'.date(DATE_FORMAT, filemtime(GALLERY_ROOT.GALLERY.'.')).'|';
        } else {
            $info = '';
        }
        if ((DIR_IMAGE_FILE) and file_exists(GALLERY_ROOT.$dir.DIR_IMAGE_FILE)) {
            return $info.$this->url_string($dir, DIR_IMAGE_FILE);
        }
        if (isset($images[0])) {
            return $info.$this->url_string($dir, $images[0]);
        }
        foreach ($dirs as $subdir) {
            $subresult = $this->_dir_info($dir.$subdir.'/', FALSE);
            if ($subresult != '') {
                return $info.$subresult;
            }
        }
        if ($initial and file_exists(GALLERY_ROOT.ICONS_DIR.DIR_IMAGE_FILE)) {
            return $info.$this->url_string(ICONS_DIR, DIR_IMAGE_FILE);
        }
        return $info;
    }

    private function _aspect_resize($image_width, $image_height, $max_width, $max_height, $enlarge) {
        if (($image_width < $max_width) and ($image_height < $max_height) and !$enlarge) {
            $new_img_height = $image_height;
            $new_img_width = $image_width;
        } else {
            $aspect_x = $image_width / $max_width;
            $aspect_y = $image_height / $max_height;
            if ($aspect_x > $aspect_y) {
                $new_img_width = $max_width;
                $new_img_height = $image_height / $aspect_x;
            } else {
                $new_img_height = $max_height;
                $new_img_width = $image_width / $aspect_y;
            }
        }
        return array($new_img_width, $new_img_height);
    }

    private function _mpo_image($file) {
        if (!$mpo = @file_get_contents($file)) {
            return false;
        }
        $offset = 0;
        $marker = true;
        $imgOffset = array();
        $markA = chr(0xFF).chr(0xD8).chr(0xFF).chr(0xE1);
        $markB = chr(0xFF).chr(0xD9).chr(0xFF).chr(0xE0);
        while ($marker!==false) {
            $marker = strpos($mpo, $markA, $offset);
            if ($marker===false) {
                $marker = strpos($mpo, $markB, $offset);
            }
            if ($marker!==false) {
                $imgOffset[] = $marker;
                $offset = $marker+4;
            }
        }
        $imgOffset[] = strlen($mpo);
        if (count($imgOffset)<2) {
            return false;
        }
        if (count($imgOffset)>2) {
            $img_left = imagecreatefromstring(substr($mpo, $imgOffset[0], $imgOffset[1]-$imgOffset[0]));
            $img_right = imagecreatefromstring(substr($mpo, $imgOffset[1], $imgOffset[2]-$imgOffset[1]));
            list($mpo_stereo_width, $mpo_stereo_height) = $this->_aspect_resize(imagesx($img_left), imagesy($img_left), MPO_STEREO_MAX_WIDTH, MPO_STEREO_MAX_HEIGHT, true);
            list($mpo_full_width, $mpo_full_height) = $this->_aspect_resize(imagesx($img_left), imagesy($img_left), MPO_FULL_MAX_WIDTH, MPO_FULL_MAX_HEIGHT, false);
            $stereo_dot_space = 0;
            if (MPO_STEREO_DOTS) {
                $dot_size=3;
                $stereo_dot_space = 2*$dot_size+2*MPO_SPACING;
            }
            $stereo_align = 0;
            $new_img_width = 0;
            $new_img_height = 0;
            $full_offset_y = 0;
            if (MPO_STEREO_IMAGE) {
                $new_img_width += $mpo_stereo_width*2+MPO_SPACING;
                $new_img_height += $stereo_dot_space + $mpo_stereo_height + (MPO_FULL_IMAGE ? MPO_SPACING : 0);
                $full_offset_y = $mpo_stereo_height+MPO_SPACING+$stereo_dot_space;
            }
            $full_offset_x = round(($new_img_width-$mpo_full_width)/2);
            if (MPO_FULL_IMAGE) {
                if ($mpo_full_width > $new_img_width) {
                    $new_img_width = $mpo_full_width;
                    $stereo_align = (int)(($mpo_full_width-($mpo_stereo_width*2+MPO_SPACING))/2);
                    $full_offset_x = 0;
                }
                $new_img_height += $mpo_full_height;
            }
            $new_image = imagecreatetruecolor($new_img_width, $new_img_height);
            $tmp_left = imagecreatetruecolor($mpo_full_width, $mpo_full_height);
            imagecopyresampled($tmp_left, $img_left, 0, 0, 0, 0, $mpo_full_width, $mpo_full_height, imagesx($img_left), imagesy($img_left));
            $tmp_right = imagecreatetruecolor($mpo_full_width, $mpo_full_height);
            imagecopyresampled($tmp_right, $img_right, 0, 0, 0, 0, $mpo_full_width, $mpo_full_height, imagesx($img_right), imagesy($img_right));
            if (MPO_FULL_IMAGE) {
                if (MPO_FULL_ANAGLYPH) {
                    $anaglyph_image = imagecreatetruecolor($mpo_full_width, $mpo_full_height);
                    imagealphablending($anaglyph_image, false);
                    for($y=0; $y<$mpo_full_height; $y++) {
                        for($x=0; $x<$mpo_full_width; $x++) {
                            $left_color = imagecolorat($tmp_left, $x, $y);
                            $r = (int)(($left_color >> 16) & 255) * 0.299 + (($left_color >> 8) & 255) * 0.587 + (($left_color) & 255) * 0.114;
                            if ($r > 255) {
                                $r = 255;
                            }
                            $g = (imagecolorat($tmp_right, $x, $y) >> 8) & 255;
                            $b = (imagecolorat($tmp_right, $x, $y)) & 255;
                            imagesetpixel($anaglyph_image, $x, $y, imagecolorallocate($anaglyph_image, $r, $g, $b));
                        }
                    }
                    imagecopyresampled($new_image, $anaglyph_image, $full_offset_x, $full_offset_y, 0, 0, $mpo_full_width, $mpo_full_height, $mpo_full_width, $mpo_full_height);
                    imagedestroy($anaglyph_image);
                    imagedestroy($tmp_left);
                    imagedestroy($tmp_right);
                } else {
                    imagecopyresampled($new_image, $img_left, $full_offset_x, $full_offset_y, 0, 0, $mpo_full_width, $mpo_full_height, imagesx($img_left), imagesy($img_left));
                }
            }
            if (MPO_STEREO_IMAGE) {
                imagecopyresampled($new_image, $img_left, $stereo_align, $stereo_dot_space, 0, 0, $mpo_stereo_width, $mpo_stereo_height, imagesx($img_left), imagesy($img_left));
                imagedestroy($img_left);
                imagecopyresampled($new_image, $img_right, $stereo_align+$mpo_stereo_width+MPO_SPACING, $stereo_dot_space, 0, 0, $mpo_stereo_width, $mpo_stereo_height, imagesx($img_right), imagesy($img_right));
                imagedestroy($img_right);
                $white = imagecolorallocate($new_image, 255, 255, 255);
                imagefilledrectangle($new_image, $stereo_align+(int)($mpo_stereo_width/2)-3, MPO_SPACING-3, $stereo_align+(int)($mpo_stereo_width/2)+3, MPO_SPACING+3, $white);
                imagefilledrectangle($new_image, $stereo_align+MPO_SPACING+(int)($mpo_stereo_width*1.5)-3, MPO_SPACING-3, $stereo_align+MPO_SPACING+(int)($mpo_stereo_width*1.5)+3, MPO_SPACING+3, $white);
            }
            return $new_image;
        } else {
            $image = imagecreatefromstring(substr($mpo, $imgOffset[0], $imgOffset[1]-$imgOffset[0]));
            list($mpo_width, $mpo_height) = $this->_aspect_resize(imagesx($image), imagesy($image), MPO_FULL_MAX_WIDTH, MPO_FULL_MAX_HEIGHT, false);
            $new_image = imagecreatetruecolor($mpo_width, $mpo_height);
            imagecopyresampled($new_image, $image, 0, 0, 0, 0, $mpo_width, $mpo_height, imagesx($image), imagesy($image));
            imagedestroy($image);
            return $new_image;
        }
    }

    private function _file_size($size) {
        $sizename = array('Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        return ($size ? round($size/pow(1024, ($i = floor(log($size, 1024)))), 2).' '.$sizename[$i] : '0 Bytes');
    }

    private function _set_dir_info($dir) {
        if (!is_dir(DATA_ROOT.'info/'.$dir)) {
            mkdir(DATA_ROOT.'info/'.$dir, 0777, TRUE);
        } else {
            $this->_clean_data_root($dir);
        }
        if ($fp = fopen(DATA_ROOT.'info/'.$dir.'_sfpg_dir', 'w')) {
            fwrite($fp, $this->_dir_info($dir));
            fclose($fp);
        }
    }

    private function _ext($file) {
        if (strrpos($file, '.') === FALSE) {
            return 'nodot';
        } else {
            return strtolower(substr($file, strrpos($file, '.')));
        }
    }

    private function _image_type($file) {
        $type = $this->_ext($file);
        if (($type == '.jpg') or ($type == '.jpeg') or ((MPO_FULL_IMAGE or MPO_STEREO_IMAGE) and ($type == '.mpo'))) {
            return 'jpeg';
        } elseif ($type == '.png') {
            return 'png';
        } elseif ($type == '.gif') {
            return 'gif';
        }
        return FALSE;
    }

    private function _array_sort(&$arr, &$arr_time, $sort_by_time, $sort_reverse) {
        if ($sort_by_time) {
            if ($sort_reverse) {
                array_multisort ($arr_time, SORT_DESC, SORT_NUMERIC, $arr);
            } else {
                array_multisort ($arr_time, SORT_ASC, SORT_NUMERIC, $arr);
            }
        }  else {
            if (SORT_NATURAL) {
                natcasesort ($arr);
                $arr = array_values($arr);
                if ($sort_reverse) {
                    $arr = array_reverse ($arr);
                }
            } else {
                if ($sort_reverse) {
                    rsort ($arr);
                } else {
                    sort ($arr);
                }
            }
        }
    }

    private function _base64url_encode($plain) {
        $base64 = base64_encode($plain);
        $base64url = strtr($base64, '+/', '-_');
        return rtrim($base64url, '=');
    }

    public function url_string($dir = '', $img = '') {
        $res = $dir.'*'.$img.'*';
        return $this->_base64url_encode($res.md5($res.SECURITY_PHRASE));
    }

    private function _str_to_script($str) {
        return str_replace("\r", "", str_replace("\n", "", str_replace("\"", "\\\"", str_replace("'", "\'", (NL_TO_BR ? nl2br($str) : $str)))));
    }


    public function url_decode($string) {
        $this->_encodedUrl = $string;
        $get = explode('*', $this->_base64url_decode($string));
        if ((md5($get[0].'*'.$get[1].'*'.SECURITY_PHRASE) === $get[2]) and (strpos($get[0].$get[1], '/../') === FALSE)) {
            return array($get[0], $get[1]);
        } else {
            return FALSE;
        }
    }

    public function display_name($name, $show_ext) {
        $break_pos = strpos($name, SORT_DIVIDER);
        if ($break_pos !== FALSE) {
            $display_name = substr($name, $break_pos + strlen(SORT_DIVIDER));
        } else {
            $display_name = $name;
        }
        if (UNDERSCORE_AS_SPACE) {
            $display_name = str_replace('_', ' ', $display_name);
        }
        if (!$show_ext) {
            $display_name = substr($display_name, 0, strrpos($display_name, '.'));
        }
        return $display_name;
    }


    public function image($image_dir, $image_file, $func, $download=FALSE) {
        if(USE_IMAGICK){
            $this->image_imagick($image_dir, $image_file, $func, $download);
        } else {
            $this->image_gd($image_dir, $image_file, $func, $download);
        }

    }


    public function image_gd($image_dir, $image_file, $func, $download=FALSE) {
        $image_path_file = DATA_ROOT.$func.'/'.$image_dir.$image_file;
        $image_type = $this->_image_type($image_file);

        if ($func == 'image') {
            if (!file_exists($image_path_file)) {
                $image_path_file = GALLERY_ROOT.$image_dir.$image_file;
            }
            if ($download) {
                if ($this->canEditHeader) {
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="'.$image_file.'"');
                }
            } else {
                if ($this->canEditHeader) {
                    header('Content-Type: image/'.$image_type);
                    header('Content-Disposition: filename="'.$image_file.'"');
                }
            }
            readfile($image_path_file);
            exit;
        }

        if (($func == 'thumb') or ($func == 'preview')) {
            if (file_exists($image_path_file)) {
                if ($this->canEditHeader) {
                    header('Content-Type: image/'.$image_type);
                    header('Content-Disposition: filename="'.$func.'_'.$image_file.'"');
                    readfile($image_path_file);
                    exit;
                }
            } else {
                if($func == 'thumb') {
                    $max_width = THUMB_MAX_WIDTH;
                    $max_height = THUMB_MAX_HEIGHT;
                    $enlarge = THUMB_ENLARGE;
                    $jpeg_quality = THUMB_JPEG_QUALITY;
                    $source_img = GALLERY_ROOT.$image_dir.$image_file;
                } else {
                    $max_width = PREVIEW_MAX_WIDTH;
                    $max_height = PREVIEW_MAX_HEIGHT;
                    $enlarge = PREVIEW_ENLARGE;
                    $jpeg_quality = PREVIEW_JPEG_QUALITY;
                    $source_img = DATA_ROOT.'image/'.$image_dir.$image_file;
                    if (!file_exists($source_img)) {
                        $source_img = GALLERY_ROOT.$image_dir.$image_file;
                    }
                }
                $image_changed = FALSE;
                if ((MPO_FULL_IMAGE or MPO_STEREO_IMAGE) and ($this->_ext($image_file)=='.mpo') and ($func != 'preview')) {
                    if (!$image = $this->_mpo_image($source_img)) {
                        exit;
                    }
                    $image_changed = TRUE;
                } elseif (!$image = imagecreatefromstring(file_get_contents($source_img))) {
                    exit;
                }

                if (($func == 'thumb') and ($image_dir != ICONS_DIR)) {
                    $this->_mkdir(DATA_ROOT.'info/'.$image_dir);
                    $exif_info = '||||||';
                    if (function_exists('exif_read_data')) {
                        if (SHOW_EXIF_INFO) {
                            $exif_data = exif_read_data(GALLERY_ROOT.$image_dir.$image_file, 'IFD0');
                            if ($exif_data !== FALSE) {
                                $exif_info = '';
                                if(isset($exif_data['DateTimeOriginal'])) {
                                    $exif_time = explode(':', str_replace(' ', ':', $exif_data['DateTimeOriginal']));
                                    $exif_info .= date(DATE_FORMAT, mktime($exif_time[3], $exif_time[4], $exif_time[5], $exif_time[1], $exif_time[2], $exif_time[0]));
                                } else {
                                    $exif_info .= 'n/a';
                                }
                                $exif_info .= '|';
                                $exif_info .= (isset($exif_data['Model'])?$exif_data['Model']:'n/a').'|';
                                $exif_info .= (isset($exif_data['ISOSpeedRatings'])?$exif_data['ISOSpeedRatings']:'n/a').'|';
                                if(isset($exif_data['ExposureTime'])) {
                                    $exif_ExposureTime=create_function('','return '.$exif_data['ExposureTime'].';');
                                    $exp_time = $exif_ExposureTime();
                                    if ($exp_time > 0.25) {
                                        $exif_info .= $exp_time;
                                    } else {
                                        $exif_info .= $exif_data['ExposureTime'];
                                    }
                                    $exif_info .= 's';
                                } else {
                                    $exif_info .= 'n/a';
                                }
                                $exif_info .= '|';
                                if(isset($exif_data['FNumber'])) {
                                    $exif_FNumber=create_function('','return number_format(round('.$exif_data['FNumber'].',1),1);');
                                    $exif_info .= 'f'.$exif_FNumber();
                                } else {
                                    $exif_info .= 'n/a';
                                }
                                $exif_info .= '|';
                                if(isset($exif_data['FocalLength'])) {
                                    $exif_FocalLength=create_function('','return number_format(round('.$exif_data['FocalLength'].',1),1);');
                                    $exif_info .= $exif_FocalLength().'mm';
                                } else {
                                    $exif_info .= 'n/a';
                                }
                                $exif_info .= '|';
                                if(isset($exif_data['Flash'])) {
                                    $exif_info .= (($exif_data['Flash'] & 1) ? TEXT_YES : TEXT_NO);
                                } else {
                                    $exif_info .= 'n/a';
                                }
                            } else {
                                $exif_info = 'sfpg_no_exif_data_in_file||||||';
                            }
                        }

                        if (ROTATE_IMAGES and isset($exif_data['Orientation'])) {
                            $image_width = imagesx($image);
                            $image_height = imagesy($image);

                            switch ($exif_data['Orientation']) {
                                case 2 : {
                                    $rotate = @imagecreatetruecolor($image_width, $image_height);
                                    if (LOW_IMAGE_RESAMPLE_QUALITY) {
                                        imagecopyresized($rotate, $image, 0, 0, $image_width-1, 0, $image_width, $image_height, -$image_width, $image_height);
                                    } else {
                                        imagecopyresampled($rotate, $image, 0, 0, $image_width-1, 0, $image_width, $image_height, -$image_width, $image_height);
                                    }
                                    imagedestroy($image);
                                    $image_changed = TRUE;
                                    break;
                                }
                                case 3 : {
                                    $rotate = imagerotate($image, 180, 0);
                                    imagedestroy($image);
                                    $image_changed = TRUE;
                                    break;
                                }
                                case 4 : {
                                    $rotate = @imagecreatetruecolor($image_width, $image_height);
                                    if (LOW_IMAGE_RESAMPLE_QUALITY) {
                                        imagecopyresized($rotate, $image, 0, 0, 0, $image_height-1, $image_width, $image_height, $image_width, -$image_height);
                                    } else {
                                        imagecopyresampled($rotate, $image, 0, 0, 0, $image_height-1, $image_width, $image_height, $image_width, -$image_height);
                                    }
                                    imagedestroy($image);
                                    $image_changed = TRUE;
                                    break;
                                }
                                case 5 : {
                                    $rotate = imagerotate($image, 270, 0);
                                    imagedestroy($image);
                                    $image = $rotate;
                                    $rotate = @imagecreatetruecolor($image_height, $image_width);
                                    if (LOW_IMAGE_RESAMPLE_QUALITY) {
                                        imagecopyresized($rotate, $image, 0, 0, 0, $image_width-1, $image_height, $image_width, $image_height, -$image_width);
                                    } else {
                                        imagecopyresampled($rotate, $image, 0, 0, 0, $image_width-1, $image_height, $image_width, $image_height, -$image_width);
                                    }
                                    $image_changed = TRUE;
                                    break;
                                }
                                case 6 : {
                                    $rotate = imagerotate($image, 270, 0);
                                    imagedestroy($image);
                                    $image_changed = TRUE;
                                    break;
                                }
                                case 7 : {
                                    $rotate = imagerotate($image, 90, 0);
                                    imagedestroy($image);
                                    $image = $rotate;
                                    $rotate = @imagecreatetruecolor($image_height, $image_width);
                                    if (LOW_IMAGE_RESAMPLE_QUALITY) {
                                        imagecopyresized($rotate, $image, 0, 0, 0, $image_width-1, $image_height, $image_width, $image_height, -$image_width);
                                    } else {
                                        imagecopyresampled($rotate, $image, 0, 0, 0, $image_width-1, $image_height, $image_width, $image_height, -$image_width);
                                    }
                                    $image_changed = TRUE;
                                    break;
                                }
                                case 8 : {
                                    $rotate = imagerotate($image, 90, 0);
                                    imagedestroy($image);
                                    $image_changed = TRUE;
                                    break;
                                }
                                default: $rotate = $image;
                            }
                            $image = $rotate;
                        }
                    }

                    $iptc_info = '|||||||||||||||||';
                    if(SHOW_IPTC_INFO) {
                        $only_used_for_iptc = getimagesize(GALLERY_ROOT.$image_dir.$image_file, $info);
                        if (isset($info['APP13'])) {
                            $iptc_info = '';
                            $iptc = iptcparse($info['APP13']);
                            $iptc_info .= (isset($iptc['2#005']) ? $iptc['2#005'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#010']) ? $iptc['2#010'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#015']) ? $iptc['2#015'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#020']) ? $iptc['2#020'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#040']) ? $iptc['2#040'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#055']) ? $iptc['2#055'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#085']) ? $iptc['2#085'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#090']) ? $iptc['2#090'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#095']) ? $iptc['2#095'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#101']) ? $iptc['2#101'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#103']) ? $iptc['2#103'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#105']) ? $iptc['2#105'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#110']) ? $iptc['2#110'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#115']) ? $iptc['2#115'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#116']) ? $iptc['2#116'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#120']) ? $iptc['2#120'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#122']) ? $iptc['2#122'][0] : 'n/a').'|';
                        } else {
                            $iptc_info = 'sfpg_no_iptc_data_in_file|||||||||||||||||';
                        }
                    }

                    if (WATERMARK) {
                        $wm_file = GALLERY_ROOT.ICONS_DIR.WATERMARK;
                        if (file_exists($wm_file)) {
                            if ($watermark = imagecreatefromstring(file_get_contents($wm_file))) {
                                $image_width = imagesx($image);
                                $image_height = imagesy($image);
                                $ww = imagesx($watermark);
                                $wh = imagesy($watermark);
                                if (WATERMARK_FRACTION) {
                                    if ($image_width < $image_height) {
                                        $ww_new = round($image_width * WATERMARK_FRACTION);
                                    } else {
                                        $ww_new = round($image_height * WATERMARK_FRACTION);
                                    }
                                    $wh_new = round($wh * ($ww_new / $ww));
                                } else {
                                    $ww_new = $ww;
                                    $wh_new = $wh;
                                }
                                imagecopyresampled ($image, $watermark, $image_width-$ww_new, $image_height-$wh_new, 0, 0, $ww_new, $wh_new, $ww, $wh);
                                imagedestroy($watermark);
                                $image_changed = TRUE;
                            }
                        }
                    }

                    if ($image_changed) {
                        $this->_mkdir(DATA_ROOT.'image/'.$image_dir);
                        $new_full_img = DATA_ROOT.'image/'.$image_dir.$image_file;
                        if ($image_type == 'jpeg') {
                            imagejpeg($image, $new_full_img, IMAGE_JPEG_QUALITY);
                        } elseif ($image_type == 'png') {
                            imagepng($image, $new_full_img);
                        } elseif ($image_type == 'gif') {
                            imagegif($image, $new_full_img);
                        }
                    }

                    $fp = fopen(DATA_ROOT.'info/'.$image_dir.$image_file, 'w');
                    fwrite($fp, date(DATE_FORMAT, filemtime(GALLERY_ROOT.$image_dir.$image_file)).'|'.$this->_file_size(filesize(GALLERY_ROOT.$image_dir.$image_file)).'|'.imagesx($image).'|'.imagesy($image).'|'.$exif_info.'|'.$iptc_info);
                    fclose($fp);
                }
                list($new_img_width, $new_img_height) = $this->_aspect_resize(imagesx($image), imagesy($image), $max_width, $max_height, $enlarge);
                $new_image = imagecreatetruecolor($new_img_width, $new_img_height);
                if (LOW_IMAGE_RESAMPLE_QUALITY) {
                    imagecopyresized($new_image, $image, 0, 0, 0, 0, $new_img_width, $new_img_height, imagesx($image), imagesy($image));
                } else {
                    imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_img_width, $new_img_height, imagesx($image), imagesy($image));
                }
                imagedestroy($image);
                $this->_mkdir(DATA_ROOT.$func.'/'.$image_dir);
                if ($this->canEditHeader) {
                    header('Content-type: image/' . $image_type);
                    header('Content-Disposition: filename="' . $func . '_' . $image_file . '"');
                    if ($image_type == 'jpeg') {
                        imagejpeg($new_image, NULL, $jpeg_quality);
                        imagejpeg($new_image, $image_path_file, $jpeg_quality);
                    } elseif ($image_type == 'png') {
                        imagepng($new_image);
                        imagepng($new_image, $image_path_file);
                    } elseif ($image_type == 'gif') {
                        imagegif($new_image);
                        imagegif($new_image, $image_path_file);
                    }
                    imagedestroy($new_image);
                } else {
                    if ($image_type == 'jpeg') {
                        imagejpeg($new_image, $image_path_file, $jpeg_quality);
                    } elseif ($image_type == 'png') {
                        imagepng($new_image, $image_path_file);
                    } elseif ($image_type == 'gif') {
                        imagegif($new_image, $image_path_file);
                    }
                    imagedestroy($new_image);
                }
            }
        }
    }

    public function image_gd_ajax($image_dir, $image_file) {
        $image_path_file_thumb = DATA_ROOT.'thumb/'.$image_dir.$image_file;
        $image_path_file_preview = DATA_ROOT.'preview/'.$image_dir.$image_file;
        $image_type = $this->_image_type($image_file);

                    $max_width_thumb = THUMB_MAX_WIDTH;
                    $max_height_thumb = THUMB_MAX_HEIGHT;
                    $enlarge_thumb = THUMB_ENLARGE;
                    $jpeg_quality_thumb = THUMB_JPEG_QUALITY;
                    $source_img = GALLERY_ROOT.$image_dir.$image_file;

                    $max_width_preview = PREVIEW_MAX_WIDTH;
                    $max_height_preview = PREVIEW_MAX_HEIGHT;
                    $enlarge_preview = PREVIEW_ENLARGE;
                    $jpeg_quality_preview = PREVIEW_JPEG_QUALITY;

                $image_changed = FALSE;
        if (!$image = imagecreatefromstring(file_get_contents($source_img))) { exit; }

        //if (($func == 'thumb') and ($image_dir != ICONS_DIR)) {
        $this->_mkdir(DATA_ROOT.'info/'.$image_dir);
            $exif_info = '||||||';
                if (function_exists('exif_read_data')) {
                    if (SHOW_EXIF_INFO) {
                        $exif_data = exif_read_data(GALLERY_ROOT.$image_dir.$image_file, 'IFD0');
                        if ($exif_data !== FALSE) {
                            $exif_info = '';
                            if(isset($exif_data['DateTimeOriginal'])) {
                                $exif_time = explode(':', str_replace(' ', ':', $exif_data['DateTimeOriginal']));
                                $exif_info .= date(DATE_FORMAT, mktime($exif_time[3], $exif_time[4], $exif_time[5], $exif_time[1], $exif_time[2], $exif_time[0]));
                            } else {
                                $exif_info .= 'n/a';
                            }
                            $exif_info .= '|';
                            $exif_info .= (isset($exif_data['Model'])?$exif_data['Model']:'n/a').'|';
                            $exif_info .= (isset($exif_data['ISOSpeedRatings'])?$exif_data['ISOSpeedRatings']:'n/a').'|';
                            if(isset($exif_data['ExposureTime'])) {
                                $exif_ExposureTime=create_function('','return '.$exif_data['ExposureTime'].';');
                                $exp_time = $exif_ExposureTime();
                                if ($exp_time > 0.25) {
                                        $exif_info .= $exp_time;
                                } else {
                                        $exif_info .= $exif_data['ExposureTime'];
                                }
                                 $exif_info .= 's';
                            } else {
                                $exif_info .= 'n/a';
                            }
                            $exif_info .= '|';
                            if(isset($exif_data['FNumber'])) {
                                $exif_FNumber=create_function('','return number_format(round('.$exif_data['FNumber'].',1),1);');
                                $exif_info .= 'f'.$exif_FNumber();
                            } else {
                                $exif_info .= 'n/a';
                            }
                            $exif_info .= '|';
                            if(isset($exif_data['FocalLength'])) {
                                $exif_FocalLength=create_function('','return number_format(round('.$exif_data['FocalLength'].',1),1);');
                                $exif_info .= $exif_FocalLength().'mm';
                            } else {
                                $exif_info .= 'n/a';
                            }
                            $exif_info .= '|';
                            if(isset($exif_data['Flash'])) {
                                $exif_info .= (($exif_data['Flash'] & 1) ? TEXT_YES : TEXT_NO);
                            } else {
                                $exif_info .= 'n/a';
                            }
                        } else {
                            $exif_info = 'sfpg_no_exif_data_in_file||||||';
                        }
                    }

                    $iptc_info = '|||||||||||||||||';
                    if(SHOW_IPTC_INFO) {
                        $only_used_for_iptc = getimagesize(GALLERY_ROOT.$image_dir.$image_file, $info);
                        if (isset($info['APP13'])) {
                            $iptc_info = '';
                            $iptc = iptcparse($info['APP13']);
                            $iptc_info .= (isset($iptc['2#005']) ? $iptc['2#005'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#010']) ? $iptc['2#010'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#015']) ? $iptc['2#015'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#020']) ? $iptc['2#020'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#040']) ? $iptc['2#040'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#055']) ? $iptc['2#055'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#085']) ? $iptc['2#085'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#090']) ? $iptc['2#090'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#095']) ? $iptc['2#095'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#101']) ? $iptc['2#101'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#103']) ? $iptc['2#103'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#105']) ? $iptc['2#105'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#110']) ? $iptc['2#110'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#115']) ? $iptc['2#115'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#116']) ? $iptc['2#116'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#120']) ? $iptc['2#120'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#122']) ? $iptc['2#122'][0] : 'n/a').'|';
                        } else {
                            $iptc_info = 'sfpg_no_iptc_data_in_file|||||||||||||||||';
                        }
                    }

                    if ($image_changed) {
                        $this->_mkdir(DATA_ROOT.'image/'.$image_dir);
                        $new_full_img = DATA_ROOT.'image/'.$image_dir.$image_file;
                        if ($image_type == 'jpeg') {
                            imagejpeg($image, $new_full_img, IMAGE_JPEG_QUALITY);
                        } elseif ($image_type == 'png') {
                            imagepng($image, $new_full_img);
                        } elseif ($image_type == 'gif') {
                            imagegif($image, $new_full_img);
                        }
                    }

                    $fp = fopen(DATA_ROOT.'info/'.$image_dir.$image_file, 'w');
                    fwrite($fp, date(DATE_FORMAT, filemtime(GALLERY_ROOT.$image_dir.$image_file)).'|'.$this->_file_size(filesize(GALLERY_ROOT.$image_dir.$image_file)).'|'.imagesx($image).'|'.imagesy($image).'|'.$exif_info.'|'.$iptc_info);
                    fclose($fp);
                }

                list($new_img_width_thumb, $new_img_height_thumb) = $this->_aspect_resize(imagesx($image), imagesy($image), $max_width_thumb, $max_height_thumb, $enlarge_thumb);
                $new_image_thumb = imagecreatetruecolor($new_img_width_thumb, $new_img_height_thumb);

                list($new_img_width_preview, $new_img_height_preview) = $this->_aspect_resize(imagesx($image), imagesy($image), $max_width_preview, $max_height_preview, $enlarge_preview);
                $new_image_preview = imagecreatetruecolor($new_img_width_preview, $new_img_height_preview);
                if (LOW_IMAGE_RESAMPLE_QUALITY) {
                    imagecopyresized($new_image_thumb, $image, 0, 0, 0, 0, $new_img_width_thumb, $new_img_height_thumb, imagesx($image), imagesy($image));
                    imagecopyresized($new_image_preview, $image, 0, 0, 0, 0, $new_img_width_preview, $new_img_height_preview, imagesx($image), imagesy($image));
                } else {
                    imagecopyresampled($new_image_thumb, $image, 0, 0, 0, 0, $new_img_width_thumb, $new_img_height_thumb, imagesx($image), imagesy($image));
                    imagecopyresampled($new_image_preview, $image, 0, 0, 0, 0, $new_img_width_preview, $new_img_height_preview, imagesx($image), imagesy($image));
                }
                imagedestroy($image);
                $this->_mkdir(DATA_ROOT.'thumb/'.$image_dir);
                $this->_mkdir(DATA_ROOT.'preview/'.$image_dir);
                    if ($image_type == 'jpeg') {
                        imagejpeg($new_image_thumb, $image_path_file_thumb, $jpeg_quality_thumb);
                        imagejpeg($new_image_preview, $image_path_file_preview, $jpeg_quality_preview);
                    } elseif ($image_type == 'png') {
                        imagepng($new_image_thumb, $image_path_file_thumb);
                        imagepng($new_image_preview, $image_path_file_preview);
                    } elseif ($image_type == 'gif') {
                        imagegif($new_image_thumb, $image_path_file_thumb);
                        imagegif($new_image_preview, $image_path_file_preview);
                    }
                imagedestroy($new_image_preview);
                imagedestroy($new_image_thumb);
    }

    public function image_imagick($image_dir, $image_file, $func, $download=FALSE) {
        $image_path_file = DATA_ABSOLUTE_ROOT.$func.'/'.$image_dir.$image_file;
        $image_type = $this->_image_type($image_file);

        if ($func == 'image') {
            if (!file_exists($image_path_file)) {
                $image_path_file = GALLERY_ROOT.$image_dir.$image_file;
            }
            if ($download) {
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.$image_file.'"');
            } else {
                header('Content-Type: image/'.$image_type);
                header('Content-Disposition: filename="'.$image_file.'"');
            }
            readfile($image_path_file);
            exit;
        }

        if (($func == 'thumb') or ($func == 'preview')) {
            if (file_exists($image_path_file)) {
                header('Content-Type: image/'.$image_type);
                header('Content-Disposition: filename="'.$func.'_'.$image_file.'"');
                readfile($image_path_file);
                exit;
            } else {
                if($func == 'thumb') {
                    $max_width = THUMB_MAX_WIDTH;
                    $max_height = THUMB_MAX_HEIGHT;
                    $source_img = GALLERY_ABSOLUTE_ROOT.$image_dir.$image_file;
                } else {
                    $max_width = PREVIEW_MAX_WIDTH;
                    $max_height = PREVIEW_MAX_HEIGHT;
                    $source_img = DATA_ABSOLUTE_ROOT.'image/'.$image_dir.$image_file;
                    if (!file_exists($source_img)) {
                        $source_img = GALLERY_ABSOLUTE_ROOT.$image_dir.$image_file;
                    }
                }
                if ((MPO_FULL_IMAGE or MPO_STEREO_IMAGE) and ($this->_ext($image_file)=='.mpo') and ($func != 'preview')) {
                    if (!$image = $this->_mpo_image($source_img)) { exit; }
                }

                if (($func == 'thumb') and ($image_dir != ICONS_DIR)) {
                    $this->_mkdir(DATA_ROOT.'info/'.$image_dir);
                    $exif_info = '||||||';
                    if (function_exists('exif_read_data')) {
                        if (SHOW_EXIF_INFO) {
                            $exif_data = exif_read_data(GALLERY_ROOT.$image_dir.$image_file, 'IFD0');
                            if ($exif_data !== FALSE) {
                                $exif_info = '';
                                if(isset($exif_data['DateTimeOriginal'])) {
                                    $exif_time = explode(':', str_replace(' ', ':', $exif_data['DateTimeOriginal']));
                                    $exif_info .= date(DATE_FORMAT, mktime($exif_time[3], $exif_time[4], $exif_time[5], $exif_time[1], $exif_time[2], $exif_time[0]));
                                } else {
                                    $exif_info .= 'n/a';
                                }
                                $exif_info .= '|';
                                $exif_info .= (isset($exif_data['Model'])?$exif_data['Model']:'n/a').'|';
                                $exif_info .= (isset($exif_data['ISOSpeedRatings'])?$exif_data['ISOSpeedRatings']:'n/a').'|';
                                if(isset($exif_data['ExposureTime'])) {
                                    $exif_ExposureTime=create_function('','return '.$exif_data['ExposureTime'].';');
                                    $exp_time = $exif_ExposureTime();
                                    if ($exp_time > 0.25) {
                                        $exif_info .= $exp_time;
                                    } else {
                                        $exif_info .= $exif_data['ExposureTime'];
                                    }
                                    $exif_info .= 's';
                                } else {
                                    $exif_info .= 'n/a';
                                }
                                $exif_info .= '|';
                                if(isset($exif_data['FNumber'])) {
                                    $exif_FNumber=create_function('','return number_format(round('.$exif_data['FNumber'].',1),1);');
                                    $exif_info .= 'f'.$exif_FNumber();
                                } else {
                                    $exif_info .= 'n/a';
                                }
                                $exif_info .= '|';
                                if(isset($exif_data['FocalLength'])) {
                                    $exif_FocalLength=create_function('','return number_format(round('.$exif_data['FocalLength'].',1),1);');
                                    $exif_info .= $exif_FocalLength().'mm';
                                } else {
                                    $exif_info .= 'n/a';
                                }
                                $exif_info .= '|';
                                if(isset($exif_data['Flash'])) {
                                    $exif_info .= (($exif_data['Flash'] & 1) ? TEXT_YES : TEXT_NO);
                                } else {
                                    $exif_info .= 'n/a';
                                }
                            } else {
                                $exif_info = 'sfpg_no_exif_data_in_file||||||';
                            }
                        }
                    }

                    $iptc_info = '|||||||||||||||||';
                    if(SHOW_IPTC_INFO) {
                        $only_used_for_iptc = getimagesize(GALLERY_ROOT.$image_dir.$image_file, $info);
                        if (isset($info['APP13'])) {
                            $iptc_info = '';
                            $iptc = iptcparse($info['APP13']);
                            $iptc_info .= (isset($iptc['2#005']) ? $iptc['2#005'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#010']) ? $iptc['2#010'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#015']) ? $iptc['2#015'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#020']) ? $iptc['2#020'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#040']) ? $iptc['2#040'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#055']) ? $iptc['2#055'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#085']) ? $iptc['2#085'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#090']) ? $iptc['2#090'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#095']) ? $iptc['2#095'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#101']) ? $iptc['2#101'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#103']) ? $iptc['2#103'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#105']) ? $iptc['2#105'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#110']) ? $iptc['2#110'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#115']) ? $iptc['2#115'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#116']) ? $iptc['2#116'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#120']) ? $iptc['2#120'][0] : 'n/a').'|';
                            $iptc_info .= (isset($iptc['2#122']) ? $iptc['2#122'][0] : 'n/a').'|';
                        } else {
                            $iptc_info = 'sfpg_no_iptc_data_in_file|||||||||||||||||';
                        }
                    }
                    $fp = fopen(DATA_ROOT.'info/'.$image_dir.$image_file, 'w');
                    $im = new Imagick();
                    $im->pingImage($source_img);
                    fwrite($fp, date(DATE_FORMAT, filemtime(GALLERY_ROOT.$image_dir.$image_file)).'|'.$this->_file_size(filesize(GALLERY_ROOT.$image_dir.$image_file)).'|'.$im->getImageWidth().'|'.$height = $im->getImageHeight().'|'.$exif_info.'|'.$iptc_info);
                    fclose($fp);
                }
                $this->_mkdir(DATA_ROOT.$func.'/'.$image_dir);
                if ($this->canEditHeader) {
                    header('Content-type: image/' . $image_type);
                    header('Content-Disposition: filename="' . $func . '_' . $image_file . '"');
                }
                $this->_resizeImage($source_img, $image_path_file, $max_width, $max_height);
            }
        }
    }

    public function get_dir($dir) {
        global $dir_exclude, $file_exclude, $file_ext_exclude;
        $dirs = array();
        $dirs_time = array();
        $images = array();
        $images_time = array();
        $files = array();
        $files_time = array();
        $misc = array();
        $directory_handle = opendir(GALLERY_ROOT.$dir);
        if ($directory_handle != FALSE) {
            while(($var=readdir($directory_handle))!==false)  {
                if (is_dir(GALLERY_ROOT.$dir.$var)) {
                    if (($var != '.') and ($var != '..') and !in_array(strtolower($var), $dir_exclude) and !@preg_match(DIR_EXCLUDE_REGEX, $var)) {
                        $dirs[] = $var;
                        if (DIR_SORT_BY_TIME)  {
                            $dirs_time[] = filemtime(GALLERY_ROOT.$dir.$var.'/.');
                        }
                    }
                }
                elseif ($this->_image_type($var)) {
                    if (($var != DIR_IMAGE_FILE) and !@preg_match(IMAGE_EXCLUDE_REGEX, $var)) {
                        if ((DELETE_IMAGE_DAYS) and (filemtime(GALLERY_ROOT.$dir.$var)<(time()-(DELETE_IMAGE_DAYS*86400)))) {
                            unlink(GALLERY_ROOT.$dir.$var);
                        } else {
                            $images[] = $var;
                            if (IMAGE_SORT_BY_TIME) {
                                $images_time[] = filemtime(GALLERY_ROOT.$dir.$var);
                            }
                        }
                    }
                }
                elseif (SHOW_FILES) {
                    if (!in_array(strtolower($var), $file_exclude) and !in_array($this->_ext($var), $file_ext_exclude) and !@preg_match(FILE_EXCLUDE_REGEX, $var)) {
                        $files[] = $var;
                        if (FILE_SORT_BY_TIME) {
                            $files_time[] = filemtime(GALLERY_ROOT.$dir.$var);
                        }
                    }
                }
                if (($this->_ext($var)==DESC_EXT)or($this->_ext($var)==PAYPAL_EXTENSION)) {
                    $misc[] = $var;
                }
            }
            if (SHOW_FILES) {
                foreach ($files as $val) {
                    $fti = array_search($val.FILE_THUMB_EXT, $images);
                    if ($fti !== FALSE) {
                        array_splice($images, $fti, 1);
                        array_splice($images_time, $fti, 1);
                    }
                }
            }
            closedir($directory_handle);
            $this->_array_sort($dirs, $dirs_time, DIR_SORT_BY_TIME, DIR_SORT_REVERSE);
            $this->_array_sort($images, $images_time, IMAGE_SORT_BY_TIME, IMAGE_SORT_REVERSE);
            $this->_array_sort($files, $files_time, FILE_SORT_BY_TIME, FILE_SORT_REVERSE);
            return array($dirs, $images, $files, $misc);
        }
        else {
            header('Location: '.$_SERVER['PHP_SELF']);
            exit;
        }
    }

    public function cron_dir($dir) {
        global $dir_exclude, $file_exclude, $file_ext_exclude;
        $dirs = array();
        $images = array();
        $directory_handle = opendir(GALLERY_ABSOLUTE_ROOT.$dir);
        if ($directory_handle != FALSE) {
            while(($var=readdir($directory_handle))!==false)  {
                if (is_dir(GALLERY_ABSOLUTE_ROOT.$dir.$var)) {
                    if (($var != '.') and ($var != '..') and !in_array(strtolower($var), $dir_exclude) and !@preg_match(DIR_EXCLUDE_REGEX, $var)) {
                        $dirs[] = $var;
                        $images = array_merge($images, $this->cron_dir($var));
                    }
                }
                elseif ($this->_image_type($var)) {
                    if (($var != DIR_IMAGE_FILE) and !@preg_match(IMAGE_EXCLUDE_REGEX, $var)) {
                        if ((DELETE_IMAGE_DAYS) and (filemtime(GALLERY_ABSOLUTE_ROOT.$dir.$var)<(time()-(DELETE_IMAGE_DAYS*86400)))) {
                            unlink(GALLERY_ABSOLUTE_ROOT.$dir.$var);
                        } else {
                            $images[] = $dir . "/" . $var;

                        }
                    }
                }
            }
            closedir($directory_handle);
            return $images;
        }
        else {
            echo "No dir present";
            exit;
        }
    }

    public function load_javascript() {
        global $dirs, $images, $files, $misc, $file_ext_thumbs;

        echo "<script language=\"JavaScript\" TYPE=\"text/javascript\">
		<!--
		var phpSelf = '".$_SERVER["PHP_SELF"]."';

		var navLink = [];
		var navName = [];

		var dirLink = [];
		var dirThumb = [];
		var dirName = [];
		var dirLocked = [];
		var dirInfo = [];

		var imgLink = [];
		var imgName = [];
		var imgInfo = [];
		var imgSell = [];

		var fileLink = [];
		var fileThumb = [];
		var fileName = [];
		var fileInfo = [];

		var imageSpace = 50;
		var slideshowActive = false;
		var slideshowSec = 0;

		var waitSpin = ['&bull;-----', '-&bull;----', '--&bull;---', '---&bull;--', '----&bull;-', '-----&bull;'];
		var waitSpinNr = 0;
		var waitSpinSpeed = 100;

		var showInfo = ".(TEXT_INFO?((isset($_GET["info"]) ? (($_GET["info"]=='1') ? "true" : "false") : (SHOW_INFO_BY_DEFAULT ? "true" : "false"))):"false").";

		var actualSize = false;
		var fullImgLoaded = false;
		var imageLargerThanViewport = false;
		var naviOk = true;
		var index = false;
		var preloadImg = new Image();
		var preloaded = -1;
		var preloadedFull = -1;

		var viewportWidth;
		var viewportHeight;

		var imgFullWidth;
		var imgFullHeight;

		";
        if (KEYBOARD_NAVIGATION)  {
            ?>
            function keyNavigate(key) {
                var _Key = (window.event) ? event.keyCode : key.keyCode;
                switch(_Key) {
                    case 33: // Page up
                    case 38: // Up arrow
                    case 37: // Left arrow
                        cycleImg(-1);
                        break;
                    case 32: // Space
                    case 34: // Page down
                    case 39: // Right arrow
                    case 40: // Down arrow
                        cycleImg(1);
                        break;
                    case 27: // ESC
                        if(index) {
                            closeImageView();
                        } else {
                            if(navLink.length>2) {
                                document.location=phpSelf+'?sfpg='+navLink[navLink.length-3]+(showInfo?'&info=1':'');
                            }
                        }
                        break;
                }
            }
            document.onkeyup = keyNavigate;
<?php   }
        echo"
		function getViewport() {
			if (typeof window.innerWidth != 'undefined') {
				viewportWidth = window.innerWidth,
				viewportHeight = window.innerHeight
			} else if (typeof document.documentElement != 'undefined' && typeof document.documentElement.clientWidth != 'undefined' && document.documentElement.clientWidth != 0) {
				viewportWidth = document.documentElement.clientWidth,
				viewportHeight = document.documentElement.clientHeight
			} else {
				viewportWidth = document.getElementsByTagName('body')[0].clientWidth,
				viewportHeight = document.getElementsByTagName('body')[0].clientHeight
			}
			if (showInfo) {
				viewportWidth -= (".INFO_BOX_WIDTH." + 12);
			}
			viewportHeight -= ".MENU_BOX_HEIGHT.";
			if (viewportHeight < 0) viewportHeight = 20;
		}

		function resizeImage() {
			var availX, availY, aspectX, aspectY, newImgX, newImgY;
			availX = viewportWidth - imageSpace;
			availY = viewportHeight - imageSpace;
			if (availX < ".THUMB_MAX_WIDTH.") {
				availX = ".THUMB_MAX_WIDTH.";
			}
			if (availY < ".THUMB_MAX_HEIGHT.") {
				availY = ".THUMB_MAX_HEIGHT.";
			}
			if ((imgFullWidth > availX) || (imgFullHeight > availY)) {
				imageLargerThanViewport = true;
			} else {
				imageLargerThanViewport = false;
			}
			if (!actualSize && ((imgFullWidth > availX) || (imgFullHeight > availY))) {
				aspectX = imgFullWidth / availX;
				aspectY = imgFullHeight / availY;
				if (aspectX > aspectY) {
					newImgX = availX;
					newImgY = Math.round(imgFullHeight / aspectX);
				} else {
					newImgX = Math.round(imgFullWidth / aspectY);
					newImgY = availY;
				}
				document.getElementById('img_resize').innerHTML = newImgX + ' x ' + newImgY;
			} else {
				newImgX = imgFullWidth;
				newImgY = imgFullHeight;
				document.getElementById('img_resize').innerHTML = '".$this->_str_to_script(TEXT_NOT_SCALED)."';
			}
			document.getElementById('img_size').innerHTML = imgFullWidth + ' x ' + imgFullHeight;
			document.getElementById('full').width = newImgX;
			document.getElementById('full').height = newImgY;
		}

		function showMenu() {
			if ((imgLink.length>0)&&naviOk) {
				menu = '<span onmouseover=\"this.className=\'sfpg_button_hover\';\" onmouseout=\"this.className=\'sfpg_button\';\" class=\"sfpg_button\" onclick=\"cycleImg(-1)\">".$this->_str_to_script(TEXT_PREVIOUS)."</span>';
				menu += '<span onmouseover=\"this.className=\'sfpg_button_hover\';\" onmouseout=\"this.className=\'sfpg_button\';\" class=\"sfpg_button\" onclick=\"cycleImg(1)\">".$this->_str_to_script(TEXT_NEXT)."</span>';
				";
        if (TEXT_SLIDESHOW)
        {
            echo"    if (slideshowActive) {
						menu += '<span onmouseover=\"this.className=\'sfpg_button_hover\';\" onmouseout=\"this.className=\'sfpg_button_on\';\" class=\"sfpg_button_on\" onclick=\"slideshowActive=false; showMenu();\">".$this->_str_to_script(TEXT_SLIDESHOW)."</span>';
                    } else {
						menu += '<span onmouseover=\"this.className=\'sfpg_button_hover\';\" onmouseout=\"this.className=\'sfpg_button\';\" class=\"sfpg_button\" onclick=\"slideshowActive=true; showMenu(); slideshow(true);\">".$this->_str_to_script(TEXT_SLIDESHOW)."</span>';
					}";
        }
        echo "
			} else {
				menu = '<span class=\"sfpg_button_disabled\">".$this->_str_to_script(TEXT_PREVIOUS)."</span>';
				menu += '<span class=\"sfpg_button_disabled\">".$this->_str_to_script(TEXT_NEXT)."</span>';
				".(TEXT_SLIDESHOW ? "menu += '<span class=\"sfpg_button_disabled\">" .$this->_str_to_script(TEXT_SLIDESHOW) . "</span>';" : "")."
			}";
        if (TEXT_INFO)
        {
            echo "
				if (showInfo) {
					menu += '<span onmouseover=\"this.className=\'sfpg_button_hover\';\" onmouseout=\"this.className=\'sfpg_button_on\';\" onclick=\"toggleInfo(showInfo);\" class=\"sfpg_button_on\">".$this->_str_to_script(TEXT_INFO)."</span>';
				} else {
					menu += '<span onmouseover=\"this.className=\'sfpg_button_hover\';\" onmouseout=\"this.className=\'sfpg_button\';\" onclick=\"toggleInfo(showInfo);\" class=\"sfpg_button\">".$this->_str_to_script(TEXT_INFO)."</span>';
				}";
        }
        echo "
			if (index && imageLargerThanViewport) {
				if (actualSize) {
					menu += '<span onmouseover=\"this.className=\'sfpg_button_hover\';\" onmouseout=\"this.className=\'sfpg_button_on\';\" class=\"sfpg_button_on\" onclick=\"fullSize()\">".$this->_str_to_script(TEXT_ACTUAL_SIZE)."</span>';
				} else {
					menu += '<span onmouseover=\"this.className=\'sfpg_button_hover\';\" onmouseout=\"this.className=\'sfpg_button\';\" class=\"sfpg_button\" onclick=\"fullSize()\">".$this->_str_to_script(TEXT_ACTUAL_SIZE)."</span>';
				}
			} else {
				menu += '<span class=\"sfpg_button_disabled\">".$this->_str_to_script(TEXT_ACTUAL_SIZE)."</span>';
			}


			";
        if (USE_PREVIEW)
        {
            echo "
				if (index) {
					if (fullImgLoaded) {
						menu += '<span onmouseover=\"this.className=\'sfpg_button_hover\';\" onmouseout=\"this.className=\'sfpg_button_on\';\" class=\"sfpg_button_on\" onclick=\"openImageView('+index+', false)\">".$this->_str_to_script(TEXT_FULLRES)."</span>';
					} else {
						menu += '<span onmouseover=\"this.className=\'sfpg_button_hover\';\" onmouseout=\"this.className=\'sfpg_button\';\" class=\"sfpg_button\" onclick=\"openImageView('+index+', true)\">".$this->_str_to_script(TEXT_FULLRES)."</span>';
					}
				} else {
					menu += '<span class=\"sfpg_button_disabled\">".$this->_str_to_script(TEXT_FULLRES)."</span>';
				}
				";
        }
        echo "
			if (index) {
                menu += '<span onmouseover=\"this.className=\'sfpg_button_hover\';\" onmouseout=\"this.className=\'sfpg_button\';\" class=\"sfpg_button\" onclick=\"downloadImage('+index+')\">".$this->_str_to_script(TEXT_DOWNLOAD_IMG)."</span>';
                menu += '<span onmouseover=\"this.className=\'sfpg_button_hover\';\" onmouseout=\"this.className=\'sfpg_button\';\" class=\"sfpg_button\" onclick=\"closeImageView()\">".$this->_str_to_script(TEXT_CLOSE_IMG_VIEW)."</span>';
			} else {
				menu += '<span class=\"sfpg_button_disabled\">".$this->_str_to_script(TEXT_CLOSE_IMG_VIEW)."</span>';
			}
			";
        if (LINK_BACK) {
            echo "menu += '<span onmouseover=\"this.className=\'sfpg_button_hover\';\" onmouseout=\"this.className=\'sfpg_button\';\" class=\"sfpg_button\" onclick=\"window.location=\'".LINK_BACK."\'\">".$this->_str_to_script(TEXT_LINK_BACK)."</span>';";
        }
        echo "
			document.getElementById('div_menu').innerHTML = menu;
		}

		function openImageView(imgId, full) {
			if (!index) {
				document.getElementById('box_overlay').style.visibility='visible';
				setOpacity('box_overlay', ".OVERLAY_OPACITY.");
			}
			index = imgId;
			fillInfo('img', index);
			setOpacity('full', 0);
			document.getElementById('wait').style.visibility='visible';
			document.getElementById('box_wait').style.visibility='visible';
			document.getElementById('box_image').style.visibility='visible';
			preloadImage(index, full);
			actualSize = false;
			fullImgLoaded = full;
			showMenu();
			showImage(0);
		}

		function fillInfo(type, id) {
			if (!index || (type == 'img')) {
				var info='<div class=\"thumbimgbox\">';
				if (type == 'dir') {
					if (dirThumb[id] != '') {
						info += '<img class=\"thumb\" alt=\"\" src=\"'+phpSelf+'?cmd=thumb&sfpg='+dirThumb[id]+'\">';
					} else {
						info += '<br><br>".$this->_str_to_script(TEXT_NO_IMAGES)."';
					}
					info += '</div>';
					info += '<strong>".$this->_str_to_script(TEXT_DIR_NAME)."</strong><br><div class=\"sfpg_info_text\">'+dirName[id] + '</div><br>';
					var splint = dirInfo[id].split('|');
					".(DIR_DESC_IN_INFO?"info += '<strong>".$this->_str_to_script(TEXT_DESCRIPTION)."</strong><br><div class=\"sfpg_info_text\">'+splint[4]+'<br></div><br>';":"")."
					info += '<strong>".$this->_str_to_script(TEXT_INFO)."</strong><br><div class=\"sfpg_info_text\">';
					info += '".$this->_str_to_script(TEXT_DATE).": '+splint[0]+'<br>';
					info += '".$this->_str_to_script(TEXT_DIRS).": '+splint[1]+'<br>';
					info += '".$this->_str_to_script(TEXT_IMAGES).": '+splint[2]+'<br>';";
        if (SHOW_FILES) {
            echo "info += '".$this->_str_to_script(TEXT_FILES).": '+splint[3]+'<br>';";
        }
        echo "
					info += '</div><br>';
					info += '<strong>".$this->_str_to_script(TEXT_LINKS)."</strong><br><a href=\"'+phpSelf+'?sfpg='+dirLink[id]+'\">".$this->_str_to_script(TEXT_DIRECT_LINK_GALLERY)."</a><br><br>';
				} else if (type == 'img') {
					info += '<img class=\"thumb\" alt=\"\" src=\"'+phpSelf+'?cmd=thumb&sfpg='+imgLink[id]+'\">';
					info += '</div>';
					var splint = imgInfo[id].split('|');
					info += '<strong>".$this->_str_to_script(TEXT_IMAGE_NAME)."</strong><br><div class=\"sfpg_info_text\">'+imgName[id] + '</div><br>';
					";

        echo"
					if (typeof splint[10] != 'undefined') {
						info += '<strong>".$this->_str_to_script(TEXT_DESCRIPTION)."</strong><br><div class=\"sfpg_info_text\">';
						info += splint[29]+'<br>';
						info += '</div><br>';

						info += '<strong>".$this->_str_to_script(TEXT_INFO)."</strong><br><div class=\"sfpg_info_text\">';
						info += '".$this->_str_to_script(TEXT_DATE).": '+splint[0]+'<br>';
						info += '".$this->_str_to_script(TEXT_IMAGESIZE).": '+splint[2]+' x '+splint[3]+'<br>';
						info += '".$this->_str_to_script(TEXT_DISPLAYED_IMAGE).": <span id=\"img_size\"></span> (';
						if (fullImgLoaded || ".(USE_PREVIEW ? "false" : "true").") {
							info += '".$this->_str_to_script(TEXT_THIS_IS_FULL)."';
						} else {
							info += '".$this->_str_to_script(TEXT_THIS_IS_PREVIEW)."';
						}
						info += ')<br>';
						info += '".$this->_str_to_script(TEXT_SCALED_TO).": <span id=\"img_resize\"></span><br>';
						info += '".$this->_str_to_script(TEXT_FILESIZE).": '+splint[1]+'<br>';
						info += '".$this->_str_to_script(TEXT_IMAGE_NUMBER).": '+id+' / '+(imgLink.length-1)+'<br>';
						info += '</div><br>';";

        if (SHOW_EXIF_INFO) {
            echo"
							info += '<strong>".$this->_str_to_script(TEXT_EXIF)."</strong><br><div class=\"sfpg_info_text\">';
							if (splint[4] == 'sfpg_no_exif_data_in_file') {
								info += '".$this->_str_to_script(TEXT_EXIF_MISSING)."';
							} else {
								info += '".$this->_str_to_script(TEXT_EXIF_DATE).": '+splint[4]+'<br>';
								info += '".$this->_str_to_script(TEXT_EXIF_CAMERA).": '+splint[5]+'<br>';
								info += '".$this->_str_to_script(TEXT_EXIF_ISO).": '+splint[6]+'<br>';
								info += '".$this->_str_to_script(TEXT_EXIF_SHUTTER).": '+splint[7]+'<br>';
								info += '".$this->_str_to_script(TEXT_EXIF_APERTURE).": '+splint[8]+'<br>';
								info += '".$this->_str_to_script(TEXT_EXIF_FOCAL).": '+splint[9]+'<br>';
								info += '".$this->_str_to_script(TEXT_EXIF_FLASH).": '+splint[10]+'<br>';
							}
							info += '</div><br>';";
        }

        if (SHOW_IPTC_INFO)
        {
            echo"           info += '<strong>".$this->_str_to_script(TEXT_IPTC)."</strong><br><div class=\"sfpg_info_text\">';
							if (splint[11] == 'sfpg_no_iptc_data_in_file') {
								info += '".$this->_str_to_script(TEXT_IPTC_MISSING)."';
							} else {
								info += '".$this->_str_to_script(TEXT_IPTC_TITLE).": '+splint[11]+'<br>';
								info += '".$this->_str_to_script(TEXT_IPTC_URGENCY).": '+splint[12]+'<br>';
								info += '".$this->_str_to_script(TEXT_IPTC_CATEGORY).": '+splint[13]+'<br>';
								info += '".$this->_str_to_script(TEXT_IPTC_SUBCATEGORIES).": '+splint[14]+'<br>';
								info += '".$this->_str_to_script(TEXT_IPTC_SPECIALINSTRUCTIONS).": '+splint[15]+'<br>';
								info += '".$this->_str_to_script(TEXT_IPTC_CREATIONDATE).": '+splint[16]+'<br>';
								info += '".$this->_str_to_script(TEXT_IPTC_AUTHORBYLINE).": '+splint[17]+'<br>';
								info += '".$this->_str_to_script(TEXT_IPTC_AUTHORTITLE).": '+splint[18]+'<br>';
								info += '".$this->_str_to_script(TEXT_IPTC_CITY).": '+splint[19]+'<br>';
								info += '".$this->_str_to_script(TEXT_IPTC_STATE).": '+splint[20]+'<br>';
								info += '".$this->_str_to_script(TEXT_IPTC_COUNTRY).": '+splint[21]+'<br>';
								info += '".$this->_str_to_script(TEXT_IPTC_OTR).": '+splint[22]+'<br>';
								info += '".$this->_str_to_script(TEXT_IPTC_HEADLINE).": '+splint[23]+'<br>';
								info += '".$this->_str_to_script(TEXT_IPTC_SOURCE).": '+splint[24]+'<br>';
								info += '".$this->_str_to_script(TEXT_IPTC_PHOTOSOURCE).": '+splint[25]+'<br>';
								info += '".$this->_str_to_script(TEXT_IPTC_COPYRIGHT).": '+splint[26]+'<br>';
								info += '".$this->_str_to_script(TEXT_IPTC_CAPTION).": '+splint[27]+'<br>';
								info += '".$this->_str_to_script(TEXT_IPTC_CAPTIONWRITER).": '+splint[28]+'<br>';
							}
							info += '</div><br>';";
        }
        echo"
					} else {
						info += '<br><strong>".$this->_str_to_script(TEXT_FIRST_VIEW)."</strong><br><br><span id=\"img_size\"></span><span id=\"img_resize\"></span><br><br>';
					}
					info += '<strong>".$this->_str_to_script(TEXT_LINKS)."</strong><br>';
					info += '<a href=\"'+phpSelf+'?sfpg='+imgLink[id]+'\">".$this->_str_to_script(TEXT_DIRECT_LINK_IMAGE)."</a><br>';
					".(TEXT_DOWNLOAD ? "info += '<a id=\"download_link_'+id+'\" href=\"'+phpSelf+'?cmd=dl&sfpg='+imgLink[id]+'\">".$this->_str_to_script(TEXT_DOWNLOAD)."</a><br><br>';" : "")."
				} else if (type == 'file') {
					if (fileThumb[id] != '') {
						info += '<img class=\"thumb\" alt=\"\" src=\"'+phpSelf+'?cmd=thumb&sfpg='+fileThumb[id]+'\">';
					} else {
						info += '<br><br>".$this->_str_to_script(TEXT_NO_PREVIEW_FILE)."<br>';
					}
					info += '</div>';
					info += '<strong>".$this->_str_to_script(TEXT_FILE_NAME)."</strong><br><div class=\"sfpg_info_text\">'+fileName[id]+'</div><br>';
					var splint = fileInfo[id].split('|');
					info += '<strong>".$this->_str_to_script(TEXT_DESCRIPTION)."</strong><br><div class=\"sfpg_info_text\">'+splint[2]+'<br></div><br>';
					info += '<strong>".$this->_str_to_script(TEXT_INFO)."</strong><br><div class=\"sfpg_info_text\">';
					info += '".$this->_str_to_script(TEXT_DATE).": '+splint[0]+'<br>';
					info += '".$this->_str_to_script(TEXT_FILESIZE).": '+splint[1]+'<br>';
					info += '</div><br>';
				}
				document.getElementById('box_inner_info').innerHTML = info;
			}
		}

		function toggleInfo(status) {
			if (status) {
				document.getElementById('box_info').style.visibility='hidden';
			} else {
				setOpacity('box_info', 0);
				document.getElementById('box_info').style.visibility='visible';
				fadeOpacity('box_info', 0,	100, ".FADE_DURATION_MS.");
			}
			showInfo = !status;
			initDisplay();
		}

		function openGallery(id, type) {
			window.location=phpSelf+'?sfpg='+((type=='nav')?navLink[id]:dirLink[id])+(showInfo?'&info=1':'');
		}

		function openFile(id) {
			if (".(FILE_IN_NEW_WINDOW ? "true" : "false").") {
				window.open(phpSelf+'?cmd=file&sfpg='+fileLink[id]);
			} else {
				window.location	= phpSelf+'?cmd=file&sfpg='+fileLink[id];
			}
		}

		function showImage(stage) {
			if(stage==0) {
				document.getElementById('full').src = '';
				naviOk=false;
				showMenu();
				stage=1;
			}
			if(stage==1) {
				if (preloadImg.complete) {
					document.getElementById('full').src = preloadImg.src;
					initDisplay();
					stage=2;
				}
			}
			if(stage==2) {
				if(document.getElementById('full').complete) {
					naviOk=true;
					imgFullWidth = preloadImg.width;
					imgFullHeight = preloadImg.height;
					fillInfo('img', index);
					initDisplay();
					preloadImage(nextImage(1),0);
					document.getElementById('wait').style.visibility='hidden';
					fadeOpacity('full', 0, 100, ".FADE_DURATION_MS.");
					stage=3;
				}
			}
			if (waitSpinNr >= waitSpin.length) {
				waitSpinNr = 0;
			}
			document.getElementById('wait').innerHTML = '<div class=\"loading\">".$this->_str_to_script(TEXT_IMAGE_LOADING)."' + waitSpin[waitSpinNr] + '</div>';
			waitSpinNr++;
			if ((stage<3) && index) {
				setTimeout ('showImage('+stage+')',waitSpinSpeed);
			}
		}

		function closeImageView() {
			slideshowActive = false;
			document.getElementById('box_wait').style.visibility='hidden';
			document.getElementById('wait').style.visibility='hidden';
			document.getElementById('box_image').style.visibility='hidden';
			index = false;
			naviOk=true;
			showMenu();
			fadeOpacity('box_overlay', ".OVERLAY_OPACITY.", 0, ".FADE_DURATION_MS.");
			document.getElementById('full').width = 1;
			document.getElementById('full').height = 1;
			document.getElementById('full').src = '';
			fillInfo('dir', 0);
		}

		function thumbDisplayName(name) {
			dispName = name.substring(0,".THUMB_CHARS_MAX.");
			if (name.length > ".THUMB_CHARS_MAX.") {
				dispName += '...';
			}
			return dispName;
		}

		function addElement(elementNumber, type) {
			var divClassName = 'thumbbox';
			var content='';
			if (type == 'dir') {
                divClassName = divClassName+' thumbbox-dir';
                if (dirLocked[elementNumber] == '1') {
			        divClassName = divClassName+' locked';
			    }
				content = '<div onclick=\"openGallery('+elementNumber+')\" onmouseover=\"this.className=\'innerboxdir_hover\'; fillInfo(\'dir\', '+elementNumber+')\" onmouseout=\"this.className=\'innerboxdir\'; fillInfo(\'dir\', 0)\" class=\"innerboxdir\"><div class=\"thumbimgbox\">';
                content += '<div class=\"folder\"></div>';
				if (dirLocked[elementNumber] == '1') {
	                content += '<div class=\"locked_bg\"></div>';
			    }
				if (dirThumb[elementNumber] != '') {
					content += '<img class=\"thumb\" alt=\"\" src=\"'+phpSelf+'?cmd=thumb&sfpg='+dirThumb[elementNumber]+'\">';
				} else {
					content += '<br><br>".$this->_str_to_script(TEXT_NO_IMAGES)."';
				}
				content += '</div>';
				". (THUMB_CHARS_MAX ? "content += '['+thumbDisplayName(dirName[elementNumber])+']';" : "")."
				content += '</div>';
			} else if (type == 'img') {
				content = '<div onclick=\"openImageView('+elementNumber+', false)\" onmouseover=\"this.className=\'innerboximg_hover\'; fillInfo(\'img\', '+elementNumber+')\" onmouseout=\"this.className=\'innerboximg\'; fillInfo(\'dir\', 0)\" class=\"innerboximg\"><div class=\"thumbimgbox\"><img class=\"thumb\" alt=\"\" src=\"'+phpSelf+'?cmd=thumb&sfpg='+imgLink[elementNumber]+'\"></div>';
				". (THUMB_CHARS_MAX ? "content += thumbDisplayName(imgName[elementNumber]);" : "")."
				content += '</div>';
			} else if (type == 'file') {
				content = '<div onclick=\"openFile('+elementNumber+')\" onmouseover=\"this.className=\'innerboxfile_hover\'; fillInfo(\'file\', '+elementNumber+')\" onmouseout=\"this.className=\'innerboxfile\'; fillInfo(\'dir\', 0)\" class=\"innerboxfile\"><div class=\"thumbimgbox\">';
				if (fileThumb[elementNumber] != '') {
					content += '<img class=\"thumb\" alt=\"\" src=\"'+phpSelf+'?cmd=thumb&sfpg='+fileThumb[elementNumber]+'\">';
				} else {
					content += '<br><br>".$this->_str_to_script(TEXT_NO_PREVIEW_FILE)."';
				}
				content += '</div>';
				". (THUMB_CHARS_MAX ? "content += thumbDisplayName(fileName[elementNumber]);" : "")."
				content += '</div>';
			} else if (".(DIR_DESC_IN_GALLERY?'true':'false')." && (type == 'desc')) {
				var splint = dirInfo[elementNumber].split('|');
				if ((typeof splint[4] != 'undefined') && (splint[4] != '')) {
					divClassName = 'descbox';
					content = '<div class=\"innerboxdesc\">';
					content += splint[4];
					content += '</div>';
				}
			}
			if (content != '') {
				var newdiv = document.createElement('div');
				newdiv.className = divClassName;
				newdiv.innerHTML = content;
				var boxC = document.getElementById('box_gallery');
				boxC.appendChild(newdiv);
			}
		}

		function showGallery(initOpenImage) {
			initDisplay();
			if (initOpenImage) {
				openImageView(initOpenImage, false);
			} else {
				fillInfo('dir', 0);
			}
			if (showInfo) {
				toggleInfo(false);
			}
			var navLinks = '';
			for (i = 1; i < navLink.length; i++) {
				if (navLink[i] != '') {
					navLinks += '<span onmouseover=\"this.className=\'sfpg_button_hover\';\" onmouseout=\"this.className=\'sfpg_button_nav\';\" class=\"sfpg_button_nav\" onclick=\"openGallery('+i+', \'nav\')\">'+navName[i]+'</span>';
				} else {
					navLinks += navName[i];
				}
			}
			document.getElementById('navi').innerHTML = navLinks;
			addElement(0, 'desc');
			for (i = 1; i < dirLink.length; i++) {
				addElement(i, 'dir');
			}

			for (i = 1; i < imgLink.length; i++) {
				addElement(i, 'img');
			}

			for (i = 1; i < fileLink.length; i++) {
				addElement(i, 'file');
			}
		}


		function slideshow(click) {
			if(slideshowActive) {
				if(click) {
					openImageView(nextImage(1),false);
					slideshowSec=0;
				}
				if(slideshowSec>=".SLIDESHOW_DELAY_SEC.") {
					if(preloadImg.complete) {
						openImageView(nextImage(1),false);
						slideshowSec=0;
					}
				}
				slideshowSec++;
				setTimeout('slideshow(false)',1000);
			} else {
				slideshowSec=0;
			}
		}

		";
        echo "navLink[1] = '".$this->url_string('')."';\n";
        echo "navName[1] = '".$this->_str_to_script(TEXT_HOME)."';\n\n";
        $links = explode("/", GALLERY);
        $gal_dirs = "";
        if (GALLERY and is_array($links)) {
            for ($i = 0; $i < count($links); $i++) {
                if ($links[$i]!=='') {
                    $gal_dirs .= $links[$i]."/";
                    $display_name = (in_array(DIR_NAME_FILE, $misc)?@file(GALLERY_ROOT.$gal_dirs.DIR_NAME_FILE):"");
                    if ($display_name) {
                        $display_name = trim($display_name[0]);
                    } else {
                        $display_name = $this->display_name($links[$i], TRUE);
                    }
                    $a_names[] = $display_name;
                    $a_links[] = $gal_dirs;
                }
            }
            $link_disp_lenght = strlen(TEXT_HOME) + 4;
            $start_link = count($a_names)-1;
            for($i = count($a_names)-1; $i >= 0; $i--) {
                $link_disp_lenght += strlen($a_names[$i]) + 5;
                if ($link_disp_lenght < NAVI_CHARS_MAX) {
                    $start_link = $i;
                }
            }
            $i = 2;
            for ($link_nr = $start_link; $link_nr < count($a_links); $link_nr++) {
                if(($start_link > 0) and ($link_nr == $start_link)) {
                    echo "navLink[".$i."] = '';\n";
                    echo "navName[".$i."] = '".$this->_str_to_script(" ... ")."';\n\n";
                    $i++;
                } else {
                    echo "navLink[".$i."] = '';\n";
                    echo "navName[".$i."] = '".$this->_str_to_script(" > ")."';\n\n";
                    $i++;
                }
                echo "navLink[".$i."] = '".$this->url_string($a_links[$link_nr])."';\n";
                echo "navName[".$i."] = '".$this->_str_to_script($a_names[$link_nr])."';\n\n";
                $i++;
            }
            echo "dirLink[0] = '".$this->url_string($a_links[count($a_links)-1])."';\n";
            echo "dirName[0] = '".$this->_str_to_script((count($a_links) == 0 ? TEXT_HOME : $a_names[count($a_links)-1]))."';\n";
            if (file_exists(GALLERY_ROOT.GALLERY."/".PASSWORD_FILE)) {
                echo "dirLocked[0] = '1';\n";
                $this->haveAcess = $this->isAuth();
            } else {
                echo "dirLocked[0] = '0';\n";
            }
        } else {
            echo "dirLink[0] = '".$this->url_string("")."';\n";
            echo "dirName[0] = '".$this->_str_to_script(TEXT_HOME)."';\n";
            echo "dirLocked[0] = '0';\n";
        }

        if (!file_exists(DATA_ROOT."info/".GALLERY."_sfpg_dir")) {
            $this->_set_dir_info(GALLERY);
        }

        $filed = explode("|", file_get_contents(DATA_ROOT."info/".GALLERY."_sfpg_dir"));
        if ((count($dirs) != $filed[0]) or (count($images) != $filed[1]) or (count($files) != $filed[2])) {
            $this->_set_dir_info(GALLERY);
            $filed = explode("|", file_get_contents(DATA_ROOT."info/".GALLERY."_sfpg_dir"));
        }
        echo "dirThumb[0] = '".$filed[4]."';\n";
        echo "dirInfo[0] = '".$this->_str_to_script($filed[3]."|".$filed[0]."|".$filed[1]."|".$filed[2]."|".(in_array(DIR_DESC_FILE, $misc)?@file_get_contents(GALLERY_ROOT.GALLERY.DIR_DESC_FILE):""))."';\n\n";

        $item = 1;
        if ($this->haveAcess) {
            foreach ($dirs as $val) {
                $display_name = @file(GALLERY_ROOT . GALLERY . $val . "/" . DIR_NAME_FILE);
                if ($display_name) {
                    $display_name = trim($display_name[0]);
                } else {
                    $display_name = $this->display_name($val, TRUE);
                }
                echo "dirName[" . ($item) . "] = '" . $this->_str_to_script($display_name) . "';\n";
                echo "dirLink[" . ($item) . "] = '" . $this->url_string((GALLERY . $val . "/")) . "';\n";
                if (file_exists(GALLERY_ROOT . GALLERY . $val . "/" . PASSWORD_FILE)) {
                    echo "dirLocked[" . ($item) . "] = '1';\n";
                } else {
                    echo "dirLocked[" . ($item) . "] = '0';\n";
                }
                if (!file_exists(DATA_ROOT . "info/" . GALLERY . $val . "/_sfpg_dir")) {
                    $this->_set_dir_info(GALLERY . $val . "/");
                }
                $filed = explode("|", file_get_contents(DATA_ROOT . "info/" . GALLERY . $val . "/_sfpg_dir"));
                echo "dirThumb[" . ($item) . "] = '" . $filed[4] . "';\n";
                echo "dirInfo[" . ($item) . "] = '" . $this->_str_to_script($filed[3] . "|" . $filed[0] . "|" . $filed[1] . "|" . $filed[2] . "|" . @file_get_contents(GALLERY_ROOT . GALLERY . $val . "/" . DIR_DESC_FILE)) . "';\n\n";
                $item++;
            }

            $img_direct_link = FALSE;
            $showImage = true;
            $item = 1;
            foreach ($images as $val) {
                if (SHOW_MAX_IMAGES) {
                    if ($item >= SHOW_MAX_IMAGES) {
                        $showImage = false;
                    }
                }
                if (SHOW_IMAGE_DAYS) {
                    if (filemtime(GALLERY_ROOT . GALLERY . $val) < (time() - (SHOW_IMAGE_DAYS * 86400))) {
                        $showImage = false;
                    }
                }
                if ($showImage) {
                    if ($val == IMAGE) {
                        $img_direct_link = ($item);
                    }
                    echo "imgLink[" . ($item) . "] = '" . $this->url_string(GALLERY, $val) . "';\n";
                    $img_name = $this->display_name($val, SHOW_IMAGE_EXT);
                    echo "imgName[" . ($item) . "] = '" . $this->_str_to_script($img_name) . "';\n";
                    echo "imgInfo[" . ($item) . "] = '" . $this->_str_to_script(@file_get_contents(DATA_ROOT . "info/" . GALLERY . $val) . "|" . (in_array($val . DESC_EXT, $misc) ? @file_get_contents(GALLERY_ROOT . GALLERY . $val . DESC_EXT) : "")) . "';\n";
                    if (PAYPAL_ENABLED) {
                        $sell = (in_array($val . PAYPAL_EXTENSION, $misc) ? @file(GALLERY_ROOT . GALLERY . $val . PAYPAL_EXTENSION, FILE_IGNORE_NEW_LINES) : false);
                        if ($sell != false) {
                            echo "imgSell[" . ($item) . "] = '" . $this->_str_to_script($sell[0]) . "|" . $this->_str_to_script($sell[1]) . "|" . $this->_str_to_script($sell[2]) . "';\n";
                        }
                    }
                    $item++;
                }
            }
        }
        if ($img_direct_link) {
            define("IMAGE_ID_IN_URL", $img_direct_link);
        } else {
            define("IMAGE_ID_IN_URL", FALSE);
        }

        $item = 1;
        foreach ($files as $val) {
            $ext = $this->_ext($val);
            echo "fileLink[".($item)."] = '".$this->url_string(GALLERY, $val)."';\n";
            if (FILE_THUMB_EXT and file_exists(GALLERY_ROOT.GALLERY.$val.FILE_THUMB_EXT)) {
                echo "fileThumb[".($item)."] = '".$this->url_string(GALLERY, $val.FILE_THUMB_EXT)."';\n";
            } elseif (isset($file_ext_thumbs[$ext])) {
                echo "fileThumb[".($item)."] = '".$this->url_string(ICONS_DIR, $file_ext_thumbs[$ext])."';\n";
            } else {
                echo "fileThumb[".($item)."] = '';\n";
            }
            echo "fileName[".($item)."] = '".$this->_str_to_script($this->display_name($val, SHOW_FILE_EXT))."';\n";
            if (!file_exists(DATA_ROOT."info/".GALLERY.$val)) {
                $fp = fopen(DATA_ROOT."info/".GALLERY.$val, "w");
                fwrite($fp, date(DATE_FORMAT, filemtime(GALLERY_ROOT.GALLERY.$val))."|".$this->_file_size(filesize(GALLERY_ROOT.GALLERY.$val)));
                fclose($fp);
            }
            echo "fileInfo[".($item)."] = '".$this->_str_to_script(@file_get_contents(DATA_ROOT."info/".GALLERY.$val)."|".(in_array($val.DESC_EXT, $misc)?@file_get_contents(GALLERY_ROOT.GALLERY.$val.DESC_EXT):""))."';\n\n";
            $item++;
        }
        echo "
		//-->
		</script>";
    }

    public function processLogIn(){
        if (isset($_POST["password"])) {
            if (file_exists(GALLERY_ROOT . GALLERY . "/" . PASSWORD_FILE)) {
                $dirPassword = trim(file_get_contents(GALLERY_ROOT . GALLERY . "/" . PASSWORD_FILE));
            }
            if($_POST["password"] == $dirPassword) {
                $_SESSION[$this->_encodedUrl] = TRUE;
                if(isset($_POST["remember_me"]) && ($_POST["remember_me"])== 'on' ){
                    setcookie($this->_encodedUrl,  md5($dirPassword), time()+3600*24*365);
                }
            } elseif(isset($_SESSION[$this->_encodedUrl."auth"])) {
                unset($_SESSION[$this->_encodedUrl]);
            }
        }
    }

    public function processPreaprePageLogIn(){
        $this->haveAcess = $this->_isPreparePageAuth();
        if (isset($_POST["password"]) && ($_POST["password"] == PREPARE_PAGE_PASSWORD)) {
            $this->haveAcess = true;
            $_SESSION[$this->_encodedUrl] = TRUE;
        }
    }

    public function isAuth(){
        if (file_exists(GALLERY_ROOT . GALLERY . "/" . PASSWORD_FILE)) {
            $dirPassword = trim(file_get_contents(GALLERY_ROOT . GALLERY . "/" . PASSWORD_FILE));
            $md5DirPassword = md5($dirPassword);
        }
        if (isset($_SESSION[$this->_encodedUrl])) {
            return $_SESSION[$this->_encodedUrl] == TRUE;
        } elseif (isset($_COOKIE[$this->_encodedUrl])) {
            return $_COOKIE[$this->_encodedUrl] == $md5DirPassword;
        } else {
            return false;
        }
    }

    private function _isPreparePageAuth(){
        if (isset($_SESSION[$this->_encodedUrl])) {
            return $_SESSION[$this->_encodedUrl] == TRUE;
        } else {
            return false;
        }
    }


    private function _resizeImage($originalImage, $newImage, $newWidth, $newHeight) {
        $im = new Imagick();
        try {
            $im->pingImage($originalImage);
        } catch (ImagickException $e) {
            throw new Exception('Invalid or corrupted image file, please try uploading another image.');
        }

        $width  = $im->getImageWidth();
        $height = $im->getImageHeight();

        list($newWidth, $newHeight) = $this->_aspect_resize($width, $height, $newWidth, $newHeight, $enlarge);

        if ($width > $newWidth || $height > $newHeight) {
            try {
                $fitbyWidth = ($newWidth / $width) > ($newHeight / $height);
                $aspectRatio = $height / $width;
                if ($fitbyWidth) {
                    $im->setSize($newWidth, abs($width * $aspectRatio));
                } else {
                    $im->setSize(abs($height / $aspectRatio), $newHeight);
                }
                $im->readImage($originalImage);
                if ($fitbyWidth) {
                    $im->thumbnailImage($newWidth, 0, false);
                } else {
                    $im->thumbnailImage(0, $newHeight, false);
                }
                $im->setImageFileName($newImage);
                $im->writeImage();
            } catch (ImagickException $e) {
                header('HTTP/1.1 500 Internal Server Error');
                throw new Exception('An error occured reszing the image.');
            }
        }
        $im->destroy();
    }
}

$manager = new Manager();