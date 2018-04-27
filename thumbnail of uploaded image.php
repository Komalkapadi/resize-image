// Function to create image

public static function cropImage($src, $dst, $thumbwidth)
    {
        $quality = 100;
        if (file_exists($src)) {
            $mimetype = mime_content_type($src);
            $arr = explode('/', $mimetype);
            if ($arr[0] == 'image' || $arr[0] == '1image') {
                if (!list($w, $h) = getimagesize($src)) {
                    return "Unsupported picture type!";
                }

                $im1 = ImageCreateFromJPEG($src);

                //if(function_exists("exif_read_data")){
                $exif = exif_read_data($src);
                if (!empty($exif['Orientation'])) {
                    switch ($exif['Orientation']) {
                        case 8:
                            $im1 = imagerotate($im1, 90, 0);
                            break;
                        case 3:
                            $im1 = imagerotate($im1, 180, 0);
                            break;
                        case 6:
                            $im1 = imagerotate($im1, -90, 0);
                            break;
                    }
                }
                //}
                $info = @getimagesize($src);

                $width = $info[0];

                $w2 = ImageSx($im1);
                $h2 = ImageSy($im1);
                $w1 = ($thumbwidth <= $info[0]) ? $thumbwidth : $info[0];

                $h1  = floor($h2 * ($w1 / $w2));
                $im2 = imagecreatetruecolor($w1, $h1);

                imagecopyresampled($im2, $im1, 0, 0, 0, 0, $w1, $h1, $w2, $h2);
                $path = addslashes($dst);
                ImageJPEG($im2, $path, $quality);
                ImageDestroy($im1);
                ImageDestroy($im2);
                return true;
            } else {
                return 'error';
            }

        }
    }
    
    
    // Below snippet to call above function
    function test(){
    
      if (!empty(request('file_name'))) {
                $image = ['image/jpeg', 'image/png', 'image/gif'];
                //$file = request('file_name')->getMimeType();
                // create image unique name
                $name      = str_replace(' ', '', $request->user()->name);
                $PostImage = $name . time() . '.' . $request->file_name->getClientOriginalExtension();
                // $request->user()->name . '_' .
                // if file is moved to destination. store file name and type in array.
                $Path = (request('post_type') == 1 ? config('constants.path.POST_PATH') : config('constants.path.GROUP_POST_PATH'));

                if ($request->file_name->move($Path, $PostImage)) {
                    $NewPost['file_type'] = request('file_type');
                    $NewPost['file_name'] = $PostImage;

                    // if shared with group, then copy file for group post too.
                    if (request('isGroupShare') == 1) {
                        $image = ['image/jpeg', 'image/png', 'image/gif'];
                        //$file = request('file_name')->getMimeType();
                        // create image unique name
                        $name      = str_replace(' ', '', $request->user()->name);
                        $PostImage = $name . time() . '.' . $request->file_name->getClientOriginalExtension();

                        $CopyPostFile = \File::copy(config('constants.path.POST_PATH') . $PostImage, config('constants.path.GROUP_POST_PATH') . $PostImage);
                    }
                }

                cropImage(config('constants.path.POST_PATH') . $PostImage,config('constants.path.POST_PATH') .'300_' .$PostImage,300);

                cropImage(config('constants.path.POST_PATH') . $PostImage,config('constants.path.POST_PATH') .'600_' .$PostImage,600);
            }
    }
