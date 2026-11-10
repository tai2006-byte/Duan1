<?php

class ProductImageUpload
{
    const MAX_BYTES = 5 * 1024 * 1024;

    public static function validate($file)
    {
        if (!is_array($file) || !isset($file['error']) || !is_int($file['error'])) {
            throw new RuntimeException('Dữ liệu ảnh tải lên không hợp lệ.');
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Tải ảnh thất bại. Hãy chọn lại ảnh và kiểm tra giới hạn dung lượng của máy chủ.');
        }
        $path = $file['tmp_name'] ?? null;
        if (!is_string($path) || !is_file($path)) {
            throw new RuntimeException('Không tìm thấy ảnh tải lên.');
        }
        $bytes = filesize($path);
        if ($bytes === false || $bytes < 1 || $bytes > self::MAX_BYTES) {
            throw new RuntimeException('Ảnh phải có dung lượng lớn hơn 0 và không quá 5 MB.');
        }
        $types = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $name = $file['name'] ?? null;
        if (!is_string($name)) throw new RuntimeException('Tên ảnh không hợp lệ.');
        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $extensions = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'];
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($path);
        $info = @getimagesize($path);
        if (!isset($extensions[$extension]) || $extensions[$extension] !== $mime
            || !isset($types[$mime]) || !$info || ($info['mime'] ?? '') !== $mime) {
            throw new RuntimeException('Chỉ chấp nhận ảnh JPEG, PNG hoặc WebP hợp lệ.');
        }
        // Giới hạn kích thước để tránh ảnh nén nhỏ nhưng tốn quá nhiều bộ nhớ.
        if ($info[0] < 1 || $info[1] < 1 || $info[0] > 6000 || $info[1] > 6000 || $info[0] * $info[1] > 12000000) {
            throw new RuntimeException('Ảnh không được vượt quá 6000 pixel mỗi chiều và 12 triệu pixel.');
        }
        return $types[$mime];
    }

    public static function save($file, $required = false)
    {
        if ($file === null || (is_array($file) && ($file['error'] ?? null) === UPLOAD_ERR_NO_FILE)) {
            if ($required) throw new RuntimeException('Vui lòng chọn ảnh sản phẩm.');
            return '';
        }
        $extension = self::validate($file);
        if (!is_uploaded_file($file['tmp_name'])) {
            throw new RuntimeException('Tệp ảnh không phải tệp tải lên hợp lệ.');
        }
        // Giải mã và ghi lại ảnh để loại bỏ nội dung lạ đính kèm trong tệp gốc.
        $image = @imagecreatefromstring(file_get_contents($file['tmp_name']));
        if ($image === false) throw new RuntimeException('Ảnh bị hỏng hoặc không thể đọc.');
        $directory = dirname(__DIR__) . '/image/';
        $filename = bin2hex(random_bytes(16)) . '.' . $extension;
        $target = $directory . $filename;
        try {
            if (!is_dir($directory) || !is_writable($directory)) {
                throw new RuntimeException('Thư mục lưu ảnh chưa sẵn sàng hoặc không có quyền ghi.');
            }
            imagealphablending($image, false);
            imagesavealpha($image, true);
            if ($extension === 'jpg') $saved = imagejpeg($image, $target, 90);
            elseif ($extension === 'png') $saved = imagepng($image, $target);
            else $saved = imagewebp($image, $target, 90);
            if (!$saved) throw new RuntimeException('Không thể lưu ảnh sản phẩm.');
        } finally {
            imagedestroy($image);
        }
        return $filename;
    }
}
