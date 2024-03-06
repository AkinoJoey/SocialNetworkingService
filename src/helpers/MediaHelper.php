<?php

namespace src\helpers;

class MediaHelper
{
    
    public static function uploadMedia(string $mediaPath, string $tmpPath): bool{
        // アップロード先のディレクトリがない場合は作成
        if (!is_dir(dirname($mediaPath))) mkdir(dirname($mediaPath), 0755, true);

        // アップロードに失敗した場合
        if (!move_uploaded_file($tmpPath, $mediaPath)) return false;

        return true;
    }
    public static function createThumbnail(string $imagePath, string $thumbnailPath): bool
    {
        $command = "magick {$imagePath} -thumbnail 720x720 {$thumbnailPath}";

        exec($command, $output, $returnCode);

        return $returnCode === 0;
    }

    public static function convertAndCompressToMp4Video(string $videoPath) : bool {
        $path_parts = pathinfo($videoPath);
        $tmpFilename = $path_parts['filename'] . "_tmp.mp4";
        $tmpPathname = $path_parts['dirname'] . '/' . $tmpFilename;

        $command = "ffmpeg -i {$videoPath} -vf scale=720:-2 -c:v libx264 -crf 23 -preset medium -maxrate 1M -bufsize 2M -y {$tmpPathname}";

        exec($command, $output, $returnCode);

        if ($returnCode === 0) {
            $newPathName = $path_parts['dirname'] . '/' . $path_parts['filename'] . ".mp4";

            // オリジナルの動画を削除
            if(unlink($videoPath)){
                // 名前を変更
                return rename($tmpPathname, $newPathName);
            }
        } else {
            return false;
        }
    }
}
