<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Response;

/** Streams uploaded images from storage/uploads (outside the web root). Nothing here can execute. */
final class MediaController
{
    private const TYPES = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'];

    public function show(string $path): Response
    {
        $path = rawurldecode($path);
        if (!preg_match('#^[a-z0-9_-]+(/[a-z0-9_-]+)*\.(jpe?g|png|webp)$#i', $path)) {
            return Response::html('Not found', 404);
        }
        $root = realpath(BASE_PATH . '/storage/uploads');
        $file = realpath($root . '/' . $path);
        if ($file === false || !str_starts_with($file, $root . DIRECTORY_SEPARATOR) || !is_file($file)) {
            return Response::html('Not found', 404);
        }

        $etag = '"' . md5($file . filemtime($file) . filesize($file)) . '"';
        if (trim((string) ($_SERVER['HTTP_IF_NONE_MATCH'] ?? '')) === $etag) {
            return (new Response('', 304))->header('ETag', $etag);
        }
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        return (new Response((string) file_get_contents($file), 200))
            ->header('Content-Type', self::TYPES[$ext])
            ->header('Cache-Control', 'public, max-age=2592000')
            ->header('ETag', $etag)
            ->header('X-Content-Type-Options', 'nosniff')
            ->header('Content-Security-Policy', "default-src 'none'");
    }
}
