<?php

/**
 * Theme filters.
 */

namespace App;

/**
 * Add "… Continued" to the excerpt.
 *
 * @return string
 */
add_filter('excerpt_more', function () {
    return sprintf(' &hellip; <a href="%s">%s</a>', get_permalink(), __('Continued', 'sage'));
});

add_filter('wp_generate_attachment_metadata', function($metadata, $attachment_id) {
    $file = get_attached_file($attachment_id);
    $image = wp_get_image_editor($file);

    if (!is_wp_error($image)) {
        // Примусове перетворення Indexed/Palette в TrueColor
        $image->set_quality(90);
        $image->save($file);

        $path = pathinfo($file);

        // WebP
        $webp_file = $path['dirname'] . '/' . $path['filename'] . '.webp';
        $image->save($webp_file, 'image/webp');
    }

    return $metadata;
}, 10, 2);

/**
 * Фільтр для автоматичної заміни зображень у контенті на <picture>
 */
add_filter('the_content', function ($content) {
    return preg_replace_callback(
        '/<img([^>]+)src=["\']([^"\']+)["\']([^>]*)>/i',
        function ($matches) {
            $img_tag = $matches[0];
            $img_url = $matches[2];

            // Отримуємо ID вкладення за URL
            $attachment_id = attachment_url_to_postid($img_url);
            if (!$attachment_id) {
                return $img_tag; // Якщо не знайдено, повертаємо оригінальний тег
            }

            // Використовуємо нашу modern_picture функцію
            return modern_picture($attachment_id, 'large', ['class' => 'responsive-img']);
        },
        $content
    );
});


