<?php

namespace App\Services;

use WP_Image_Editor;

class ModernImageHandler {
    private $supported_formats;

    public function __construct() {
        $this->supported_formats = [
            'avif' => [
                'mime_type' => 'image/avif',
                'quality'   => 80,
                'priority'  => 1
            ],
            'webp' => [
                'mime_type' => 'image/webp',
                'quality'   => 85,
                'priority'  => 2
            ],
            'jpg' => [
                'mime_type' => 'image/jpeg',
                'quality'   => 90,
                'priority'  => 3
            ]
        ];

        add_action('init', [$this, 'initModernFormats']);
        add_filter('wp_handle_upload', [$this, 'handleImageUpload']);
        add_filter('image_editor_output_format', [$this, 'addModernFormats'], 10, 3);
        add_filter('wp_get_attachment_image', [$this, 'modifyAttachmentImage'], 10, 5);
    }

    public function initModernFormats() {
        add_filter('upload_mimes', function($mimes) {
            $mimes['avif'] = 'image/avif';
            $mimes['webp'] = 'image/webp';
            return $mimes;
        });
    }

    public function handleImageUpload($upload) {
        if (!strstr($upload['type'], 'image/')) {
            return $upload;
        }

        $file = $upload['file'];
        $image = wp_get_image_editor($file);

        if (!is_wp_error($image)) {
            foreach ($this->supported_formats as $format => $settings) {
                if ($format === pathinfo($file, PATHINFO_EXTENSION)) {
                    continue;
                }

                $new_file = preg_replace('/\.[^.]+$/', '.' . $format, $file);
                $image->save($new_file, $settings['mime_type']);
            }
        }

        return $upload;
    }

    public function addModernFormats($formats, $filename, $mime_type) {
        if (strstr($mime_type, 'image/')) {
            return array_merge($formats, array_keys($this->supported_formats));
        }
        return $formats;
    }

    public function modifyAttachmentImage($html, $attachment_id, $size, $icon, $attr) {
        if (!strstr($html, '<img')) {
            return $html;
        }

        $image_meta = wp_get_attachment_metadata($attachment_id);
        if (!$image_meta) {
            return $html;
        }

        $upload_dir = wp_upload_dir();
        $base_url = $upload_dir['baseurl'] . '/' . dirname($image_meta['file']) . '/';

        $sources = [];
        foreach ($this->supported_formats as $format => $settings) {
            $file = preg_replace('/\.[^.]+$/', '.' . $format, $image_meta['file']);
            if (file_exists($upload_dir['basedir'] . '/' . $file)) {
                $sources[] = sprintf(
                    '<source type="%s" srcset="%s">',
                    esc_attr($settings['mime_type']),
                    esc_url($base_url . basename($file))
                );
            }
        }

        if (!empty($sources)) {
            $picture = sprintf(
                '<picture>%s%s</picture>',
                implode('', $sources),
                $html
            );
            return $picture;
        }

        return $html;
    }

    public static function addImageFormatsDetection() {
        ?>
        <script>
            function checkImageFormat(format) {
                const img = new Image();
                try {
                    img.src = `data:image/${format};base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=`;
                    return img.width === 1;
                } catch(e) {
                    return false;
                }
            }

            const formats = {
                avif: checkImageFormat('avif'),
                webp: checkImageFormat('webp')
            };

            document.documentElement.className += Object.entries(formats)
                .filter(([, supported]) => supported)
                .map(([format]) => ` ${format}-support`)
                .join('');
        </script>
        <?php
    }
}
