<?php

namespace App\Providers;

use Roots\Acorn\Sage\SageServiceProvider;

class ThemeServiceProvider extends SageServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        parent::register();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        // Додаємо поле зображення до пункту меню
        add_filter('wp_setup_nav_menu_item', function ($item) {
            $item->menu_image = get_post_meta($item->ID, '_menu_image', true);
            return $item;
        });

        // Додаємо інпут для завантаження зображення в налаштування пункту меню

        add_action('wp_nav_menu_item_custom_fields', function ($item_id, $item, $depth, $args) {
            require_once get_stylesheet_directory() . '/app/helpers.php';
            $script_path = asset_path('admin-media-uploader.js');
            ?>
            <p class="description description-wide">
                <label for="menu-item-image-<?php echo $item_id; ?>">
                    <?php _e('Image URL', 'sage'); ?><br>
                    <input type="text"
                           id="menu-item-image-<?php echo $item_id; ?>"
                           class="widefat menu-item-image"
                           name="menu-item-image[<?php echo $item_id; ?>]"
                           value="<?php echo esc_attr($item->menu_image); ?>">
                    <button id="<?php echo $item_id; ?>" 
                            class="button upload-menu-image upldimg">
                        <?php _e('Upload Image', 'sage'); ?>
                    </button>
                </label>
            </p>
            <script>
                    (()=>{
                     const uploadButton = document.getElementById("<?php echo $item_id; ?>")
                       if(uploadButton){
                        uploadButton.addEventListener('click', (e)=>{
                            e.preventDefault();
                            console.log('click button id:', <?php echo $item_id; ?>)
                            const input = uploadButton.parentElement.querySelector('.menu-item-image');
        
                            // Якщо frame вже створено для цієї кнопки, відкриваємо його
                            if (uploadButton.myMediaFrame) {
                                uploadButton.myMediaFrame.open();
                            return;
                            }
                                    // Створюємо новий media frame
                            const frame = wp.media({
                            title: 'Select or Upload Image',
                            button: {
                                text: 'Use this image'
                            },
                            multiple: false
                            });
                            
                            // При виборі зображення отримуємо URL та встановлюємо значення у текстове поле
                            frame.on('select', function() {
                            const attachment = frame.state().get('selection').first().toJSON();
                            input.value = attachment.url;
                            });
                            
                            // Зберігаємо frame у властивості кнопки для повторного використання
                            uploadButton.myMediaFrame = frame;
                            frame.open();
                        })
                       }
                    })()
            </script>
            <?php
        }, 10, 4);

        // Зберігаємо URL зображення при збереженні меню
        add_action('save_post', function ($post_id) {
            if (isset($_POST['menu-item-image'])) {
                foreach ($_POST['menu-item-image'] as $item_id => $image_url) {
                    update_post_meta($item_id, '_menu_image', sanitize_text_field($image_url));
                }
            }
        });

        add_filter('walker_nav_menu_start_el', function ($item_output, $item, $depth, $args) {
            if (!empty($item->menu_image) && $item->menu_item_parent != 0) {
                // Додаємо HTML зображення після тексту пункту меню, якщо є батьківський елемент
                $image_html = sprintf(
                    '<img src="%s" alt="%s" class=" menu-item-image group-hover/hasparent:inline-block absolute left-full hidden" />',
                    esc_url($item->menu_image),
                    esc_attr($item->title)
                );
                $item_output .= $image_html;
            }
            return $item_output;
        }, 10, 4);

        add_action('admin_enqueue_scripts', function() {
            require_once get_stylesheet_directory() . '/app/helpers.php';
            
            $script_path = asset_path('admin-media-uploader.js');
            error_log('Asset path: ' . $script_path); // Записуємо шлях у лог
            if (get_current_screen()->base === 'nav-menus') {
                wp_enqueue_media();
                // wp_enqueue_script(
                //     'custom-menu-media-uploader',
                //     $script_path,
                //     ['customize-preview'],
                //     null,
                //     true
                // );
            }

        });
        parent::boot();
    }
}

