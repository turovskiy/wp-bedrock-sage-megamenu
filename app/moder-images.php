<?php

namespace App;

/**
 * Генерує тег <picture> з підтримкою AVIF і WebP, різними розмірами та оптимізацією
 *
 * @param int    $attachment_id ID зображення
 * @param string $size          Розмір зображення (default: 'full')
 * @param array  $attrs         Додаткові атрибути для тегу img
 * @return string               HTML код з тегом picture
 * @throws Exception
 */
function modern_picture($attachment_id, $size = 'full', $attrs = []) {
    try {
        // Кешуємо результат перевірки існування файлів
        static $format_cache = [];
        
        // Додаємо lazy loading за замовчуванням, якщо не вказано інше
        if (!isset($attrs['loading'])) {
            $attrs['loading'] = 'lazy';
        }
        
        // Додаємо можливість модифікувати атрибути через фільтр
        $attrs = apply_filters('modern_picture_attributes', $attrs, $attachment_id, $size);
        
        // Отримуємо базове зображення
        $html = wp_get_attachment_image($attachment_id, $size, false, $attrs);
        
        // Якщо WordPress вже обгорнув у <picture>, просто повертаємо HTML
        if (strpos($html, '<picture>') !== false) {
            return $html;
        }

        // Отримуємо інформацію про директорію завантажень
        $upload_dir = wp_upload_dir();
        if (isset($upload_dir['error']) && $upload_dir['error']) {
            throw new Exception('Upload directory error: ' . $upload_dir['error']);
        }

        // Отримуємо метадані зображення
        $image_meta = wp_get_attachment_metadata($attachment_id);
        if (!$image_meta) {
            throw new Exception('No attachment metadata found for ID: ' . $attachment_id);
        }

        // Отримуємо розміри зображення
        $size_array = wp_get_attachment_image_src($attachment_id, $size);
        if (!$size_array) {
            throw new Exception('Cannot get image dimensions for ID: ' . $attachment_id);
        }

        // Формуємо базові шляхи
        $base_url = trailingslashit($upload_dir['baseurl']) . dirname($image_meta['file']) . '/';
        $base_path = trailingslashit($upload_dir['basedir']) . dirname($image_meta['file']) . '/';

        // Додаємо можливість модифікувати формати через фільтр
        $formats = apply_filters('modern_picture_formats', [
            'avif' => [
                'mime' => 'image/avif',
                'media' => '(min-width: 1px)', // Завжди показуємо AVIF, якщо підтримується
            ],
            'webp' => [
                'mime' => 'image/webp',
                'media' => '', // Показуємо WebP як фоллбек
            ],
        ]);

        $sources = '';
        $cache_key = $base_path . basename($image_meta['file']);

        // Отримуємо всі доступні розміри зображення
        $available_sizes = [];
        if (isset($image_meta['sizes'])) {
            foreach ($image_meta['sizes'] as $size_name => $size_data) {
                $available_sizes[] = $size_data['width'];
            }
        }
        $available_sizes[] = $image_meta['width']; // Додаємо оригінальний розмір
        sort($available_sizes); // Сортуємо розміри

        // Отримуємо атрибут sizes з оригінального тегу img
        $sizes_attr = '';
        if (preg_match('/sizes="([^"]+)"/', $html, $matches)) {
            $sizes_attr = sprintf(' sizes="%s"', esc_attr($matches[1]));
        }

        foreach ($formats as $format => $format_data) {
            $srcset_values = [];
            
            // Генеруємо srcset для кожного розміру
            foreach ($available_sizes as $width) {
                $filename = preg_replace(
                    '/\.[^.]+$/', 
                    '-' . $width . 'x' . round($width * ($size_array[2] / $size_array[1])) . '.' . $format,
                    basename($image_meta['file'])
                );
                
                $full_path = $base_path . $filename;
                
                // Перевіряємо кеш
                if (!isset($format_cache[$cache_key][$format][$width])) {
                    $format_cache[$cache_key][$format][$width] = file_exists($full_path);
                    
                    // Якщо файл не існує, спробуємо згенерувати його
                    if (!$format_cache[$cache_key][$format][$width]) {
                        $format_cache[$cache_key][$format][$width] = generate_image_format(
                            $attachment_id,
                            $format,
                            $width
                        );
                    }
                }
                
                if ($format_cache[$cache_key][$format][$width]) {
                    $srcset_values[] = esc_url($base_url . $filename) . ' ' . $width . 'w';
                }
            }
            
            if (!empty($srcset_values)) {
                $media_attr = !empty($format_data['media']) 
                    ? sprintf(' media="%s"', esc_attr($format_data['media'])) 
                    : '';
                    
                $sources .= sprintf(
                    '<source type="%s" srcset="%s"%s%s>',
                    esc_attr($format_data['mime']),
                    implode(', ', $srcset_values),
                    $sizes_attr,
                    $media_attr
                );
            }
        }

        // Якщо немає жодного додаткового формату, повертаємо оригінальне зображення
        if (empty($sources)) {
            return $html;
        }

        // Додаємо можливість модифікувати фінальний HTML
        return apply_filters(
            'modern_picture_html',
            sprintf('<picture>%s%s</picture>', $sources, $html),
            $attachment_id,
            $size
        );

    } catch (Exception $e) {
        // Логуємо помилку
        error_log('Modern Picture Error: ' . $e->getMessage());
        
        // Повертаємо стандартне зображення у випадку помилки
        return $html;
    }
}

/**
 * Генерує зображення в потрібному форматі та розмірі
 *
 * @param int    $attachment_id ID зображення
 * @param string $format        Формат (avif/webp)
 * @param int    $width         Ширина
 * @return bool                 Успішність генерації
 */
function generate_image_format($attachment_id, $format, $width) {
    try {
        // Отримуємо шлях до оригінального файлу
        $file_path = get_attached_file($attachment_id);
        if (!$file_path || !file_exists($file_path)) {
            throw new Exception('Original file not found');
        }

        // Створюємо GD ресурс з оригінального зображення
        $mime_type = wp_get_image_mime($file_path);
        $image = null;

        switch ($mime_type) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($file_path);
                break;
            case 'image/png':
                $image = imagecreatefrompng($file_path);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($file_path);
                break;
            default:
                throw new Exception('Unsupported image type: ' . $mime_type);
        }

        if (!$image) {
            throw new Exception('Failed to create image resource');
        }

        // Змінюємо розмір, якщо потрібно
        $orig_width = imagesx($image);
        $orig_height = imagesy($image);
        $height = round($width * ($orig_height / $orig_width));

        $resized = imagecreatetruecolor($width, $height);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        
        // Заповнюємо прозорим фоном
        $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
        imagefilledrectangle($resized, 0, 0, $width, $height, $transparent);
        
        imagecopyresampled(
            $resized,
            $image,
            0, 0, 0, 0,
            $width, $height,
            $orig_width, $orig_height
        );

        // Визначаємо шлях для нового файлу
        $upload_dir = wp_upload_dir();
        $image_meta = wp_get_attachment_metadata($attachment_id);
        $base_path = trailingslashit($upload_dir['basedir']) . dirname($image_meta['file']) . '/';
        
        $new_filename = preg_replace(
            '/\.[^.]+$/',
            '-' . $width . 'x' . $height . '.' . $format,
            basename($image_meta['file'])
        );
        
        $new_path = $base_path . $new_filename;

        // Зберігаємо у новому форматі
        $success = false;
        switch ($format) {
            case 'webp':
                $success = imagewebp($resized, $new_path, 80);
                break;
            case 'avif':
                if (function_exists('imageavif')) {
                    $success = imageavif($resized, $new_path, 80);
                }
                break;
        }

        // Очищаємо пам'ять
        imagedestroy($image);
        imagedestroy($resized);

        return $success;

    } catch (Exception $e) {
        error_log('Generate Image Format Error: ' . $e->getMessage());
        return false;
    }
}