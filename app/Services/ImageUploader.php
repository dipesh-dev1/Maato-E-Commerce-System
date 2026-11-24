<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Accepts JPEG/PNG/WebP only, judged by the file's real bytes (finfo + getimagesize), not its name or
 * the browser-sent type. Files get random names under storage/uploads, which is outside the web root.
 * With GD available, images are re-encoded (stripping metadata and anything appended to the pixels)
 * and a thumbnail is generated.
 */
final class ImageUploader
{
    private const TYPES = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    private const EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];
    private const MAX_PIXELS = 40_000_000;

    /** @return array{path: string, thumb: string} paths relative to storage/uploads */
    public function store(array $file, string $folder, int $maxWidth = 1600, int $thumbWidth = 480): array
    {
        $this->assertValid($file);
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        $ext = self::TYPES[$mime] ?? null;
        $info = @getimagesize($file['tmp_name']);
        if ($ext === null || $info === false || $info['mime'] !== $mime) {
            throw new UploadException('Please upload a JPG, PNG or WebP image.');
        }
        if ($info[0] * $info[1] > self::MAX_PIXELS) {
            throw new UploadException('That image is too large in pixels. Please resize it under 6000 × 6000.');
        }

        $dir = config('app.uploads.path') . '/' . $folder;
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $name = bin2hex(random_bytes(16));
        $path = "{$folder}/{$name}.{$ext}";
        $thumb = "{$folder}/{$name}-thumb.{$ext}";
        $base = config('app.uploads.path') . '/';

        if (self::gdAvailable()) {
            $image = $this->open($file['tmp_name'], $mime);
            $this->save($this->resize($image, $maxWidth), $base . $path, $mime);
            $this->save($this->resize($image, $thumbWidth), $base . $thumb, $mime);
            imagedestroy($image);
        } else {
            if (!move_uploaded_file($file['tmp_name'], $base . $path)) {
                throw new UploadException('We could not save that file. Please try again.');
            }
            copy($base . $path, $base . $thumb);
        }
        return ['path' => $path, 'thumb' => $thumb];
    }

    public function delete(?string ...$paths): void
    {
        $root = realpath(config('app.uploads.path'));
        foreach ($paths as $path) {
            if (!$path) {
                continue;
            }
            $full = realpath(config('app.uploads.path') . '/' . $path);
            if ($full && $root && str_starts_with($full, $root . DIRECTORY_SEPARATOR) && is_file($full)) {
                unlink($full);
            }
        }
    }

    public static function gdAvailable(): bool
    {
        return function_exists('imagecreatetruecolor') && function_exists('imagecreatefromjpeg');
    }

    private function assertValid(array $file): void
    {
        $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;
        if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
            throw new UploadException('That file is too big. The limit is ' . $this->limitMb() . ' MB.');
        }
        if ($error !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
            throw new UploadException('The upload did not complete. Please try again.');
        }
        if ($file['size'] > config('app.uploads.max_bytes')) {
            throw new UploadException('That file is too big. The limit is ' . $this->limitMb() . ' MB.');
        }
        $ext = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, self::EXTENSIONS, true)) {
            throw new UploadException('Please upload a JPG, PNG or WebP image.');
        }
    }

    private function limitMb(): int
    {
        return (int) (config('app.uploads.max_bytes') / 1048576);
    }

    private function open(string $file, string $mime): \GdImage
    {
        $image = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($file),
            'image/png'  => @imagecreatefrompng($file),
            'image/webp' => @imagecreatefromwebp($file),
        };
        if (!$image) {
            throw new UploadException('That image appears to be damaged. Please try another.');
        }
        if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
            $image = $this->applyOrientation($image, $file);
        }
        return $image;
    }

    /** Phone photos store rotation in EXIF; re-encoding drops EXIF, so apply it to the pixels first. */
    private function applyOrientation(\GdImage $image, string $file): \GdImage
    {
        $orientation = (int) (@exif_read_data($file)['Orientation'] ?? 1);
        $angle = [3 => 180, 6 => -90, 8 => 90][$orientation] ?? 0;
        return $angle ? (imagerotate($image, $angle, 0) ?: $image) : $image;
    }

    private function resize(\GdImage $image, int $maxWidth): \GdImage
    {
        $w = imagesx($image);
        $h = imagesy($image);
        $scale = min(1, $maxWidth / $w);
        $nw = max(1, (int) round($w * $scale));
        $nh = max(1, (int) round($h * $scale));
        $canvas = imagecreatetruecolor($nw, $nh);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        imagecopyresampled($canvas, $image, 0, 0, 0, 0, $nw, $nh, $w, $h);
        return $canvas;
    }

    private function save(\GdImage $image, string $target, string $mime): void
    {
        $ok = match ($mime) {
            'image/jpeg' => imagejpeg($image, $target, 82),
            'image/png'  => imagepng($image, $target, 6),
            'image/webp' => imagewebp($image, $target, 80),
        };
        imagedestroy($image);
        if (!$ok) {
            throw new UploadException('We could not save that image. Please try again.');
        }
    }
}
