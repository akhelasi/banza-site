<?php

declare(strict_types=1);

function upload_base_dir(): string
{
    return dirname(__DIR__) . '/uploads';
}

function upload_public_path(string $relativePath): string
{
    return 'uploads/' . ltrim($relativePath, '/');
}

function allowed_upload_mimes(): array
{
    return [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];
}

function upload_max_dimensions(): array
{
    return [
        'width' => 6000,
        'height' => 6000,
    ];
}

function sanitize_upload_name(string $name): string
{
    $name = pathinfo($name, PATHINFO_FILENAME);
    $name = strtolower($name);
    $name = preg_replace('/[^a-z0-9]+/i', '-', $name) ?? 'image';
    $name = trim($name, '-');
    return $name !== '' ? substr($name, 0, 80) : 'image';
}

function store_uploaded_image(array $file, ?string &$error = null): ?string
{
    $error = null;

    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        $error = 'ფაილის ატვირთვა ვერ მოხერხდა.';
        return null;
    }

    if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
        $error = 'სურათი 5MB-ზე დიდი არ უნდა იყოს.';
        return null;
    }

    $tmpName = (string) ($file['tmp_name'] ?? '');
    if ($tmpName === '' || !is_uploaded_file($tmpName)) {
        $error = 'ატვირთული ფაილი ვერ დადასტურდა.';
        return null;
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($tmpName);
    $allowed = allowed_upload_mimes();

    if (!is_string($mime) || !isset($allowed[$mime])) {
        $error = 'დაშვებულია მხოლოდ JPG, PNG, WEBP ან GIF სურათი.';
        return null;
    }

    $imageSize = @getimagesize($tmpName);
    if ($imageSize === false) {
        $error = 'ფაილი ვალიდური სურათი არ არის.';
        return null;
    }

    $maxDimensions = upload_max_dimensions();
    $width = (int) ($imageSize[0] ?? 0);
    $height = (int) ($imageSize[1] ?? 0);
    if ($width > $maxDimensions['width'] || $height > $maxDimensions['height']) {
        $error = 'სურათის ზომები ძალიან დიდია. მაქსიმუმია ' . $maxDimensions['width'] . 'x' . $maxDimensions['height'] . ' px.';
        return null;
    }

    $subdir = date('Y/m');
    $targetDir = upload_base_dir() . '/' . $subdir;
    if (!is_dir($targetDir) && !mkdir($targetDir, 0775, true)) {
        $error = 'ატვირთვის ფოლდერი ვერ შეიქმნა.';
        return null;
    }

    $baseName = sanitize_upload_name((string) ($file['name'] ?? 'image'));
    $fileName = $baseName . '-' . bin2hex(random_bytes(5)) . '.' . $allowed[$mime];
    $target = $targetDir . '/' . $fileName;

    if (!move_uploaded_file($tmpName, $target)) {
        $error = 'ფაილის შენახვა ვერ მოხერხდა.';
        return null;
    }

    return upload_public_path($subdir . '/' . $fileName);
}

function normalize_files_array(array $files): array
{
    $normalized = [];
    $names = $files['name'] ?? [];

    if (!is_array($names)) {
        return [$files];
    }

    foreach ($names as $index => $name) {
        $normalized[] = [
            'name' => $name,
            'type' => $files['type'][$index] ?? '',
            'tmp_name' => $files['tmp_name'][$index] ?? '',
            'error' => $files['error'][$index] ?? UPLOAD_ERR_NO_FILE,
            'size' => $files['size'][$index] ?? 0,
        ];
    }

    return $normalized;
}

function list_uploaded_images(): array
{
    $base = upload_base_dir();
    if (!is_dir($base)) {
        return [];
    }

    $items = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS));
    foreach ($iterator as $file) {
        if (!$file instanceof SplFileInfo || !$file->isFile()) {
            continue;
        }

        $relative = str_replace('\\', '/', substr($file->getPathname(), strlen($base) + 1));
        if ($relative === '.gitkeep') {
            continue;
        }

        $items[] = [
            'path' => upload_public_path($relative),
            'name' => $file->getFilename(),
            'size' => $file->getSize(),
            'modified' => date('Y-m-d H:i', $file->getMTime()),
        ];
    }

    usort($items, static fn (array $a, array $b): int => strcmp($b['modified'], $a['modified']));
    return $items;
}
