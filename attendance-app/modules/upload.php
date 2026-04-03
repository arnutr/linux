<?php
require_once __DIR__ . '/../config/config.php';

function handle_checkin_upload(array $file): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'error' => 'Photo upload failed.'];
    }

    if (($file['size'] ?? 0) > MAX_UPLOAD_SIZE) {
        return ['ok' => false, 'error' => 'File too large.'];
    }

    $originalName = $file['name'] ?? '';
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_UPLOAD_EXTENSIONS, true)) {
        return ['ok' => false, 'error' => 'Invalid file extension.'];
    }

    $mime = mime_content_type($file['tmp_name']);
    if (!in_array($mime, ALLOWED_UPLOAD_MIME, true)) {
        return ['ok' => false, 'error' => 'Invalid MIME type.'];
    }

    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }

    $safeName = sprintf('%s_%s.%s', date('YmdHis'), bin2hex(random_bytes(12)), $ext);
    $destination = UPLOAD_DIR . $safeName;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return ['ok' => false, 'error' => 'Could not save uploaded file.'];
    }

    return ['ok' => true, 'filename' => $safeName];
}
