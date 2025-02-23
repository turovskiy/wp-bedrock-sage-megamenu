
# Документація налаштування теми

Цей файл містить опис основних частин коду для налаштування теми WordPress. Тут описані дії для реєстрації активів, налаштування підтримки теми, реєстрації меню, віджетів та кастомних налаштувань, а також інтеграції з Blade для роботи з кастомним шаблонізатором.

---

1. **Перехід до директорії теми:**  
   Виконайте команду:
   ```bash
   cd [theme_path]
   ```  
   де `[theme_path]` — шлях до папки з вашою темою.

2. **Клонування репозиторію:**  
   Виконайте команду:
   ```bash
   git clone https://github.com/turovskiy/wp-bedrock-sage-megamenu
   ```  
3. **Встановлення PHP залежностей:**  
   Виконайте команду:
   ```bash
   composer install
   ```  
   Ця команда встановить всі необхідні пакети, зазначені у файлі `composer.json`.

4. **Встановлення JavaScript залежностей:**  
   Виконайте команду:
   ```bash
   yarn
   ```  
   Ця команда завантажить і встановить усі залежності, що описані в `package.json`.

5. **Компіляція активів теми:**  
   Після встановлення залежностей запустіть:
   ```bash
   yarn build
   ```  
   Це забезпечить збірку, мінімізацію та оптимізацію файлів (JavaScript, CSS) для роботи теми.


## Зміст
- [Документація налаштування теми](#документація-налаштування-теми)
  - [Зміст](#зміст)
  - [Реєстрація активів теми](#реєстрація-активів-теми)
  - [Налаштування теми](#налаштування-теми)
  - [Підтримка кастомного логотипа та заголовного зображення](#підтримка-кастомного-логотипа-та-заголовного-зображення)
    - [Кастомний логотип](#кастомний-логотип)
    - [Заголовне зображення](#заголовне-зображення)
  - [Реєстрація віджетів](#реєстрація-віджетів)
  - [Ініціалізація ModernImageHandler](#ініціалізація-modernimagehandler)
  - [Blade директива `modernPicture`](#blade-директива-modernpicture)
  - [Джерела](#джерела)
- [Документація функції asset\_path](#документація-функції-asset_path)
  - [Алгоритм роботи](#алгоритм-роботи)
  - [Параметри](#параметри)
  - [Повертає](#повертає)
  - [Приклад використання](#приклад-використання)
  - [Важливі моменти](#важливі-моменти)
- [Документація ThemeServiceProvider](#документація-themeserviceprovider)
  - [Огляд класу](#огляд-класу)
  - [Метод register](#метод-register)
  - [Метод boot](#метод-boot)
    - [Додавання поля зображення до пунктів меню](#додавання-поля-зображення-до-пунктів-меню)
    - [Відображення кастомних полів у меню](#відображення-кастомних-полів-у-меню)
    - [JavaScript для завантажувача зображень](#javascript-для-завантажувача-зображень)
    - [Збереження URL зображення](#збереження-url-зображення)
    - [Вивід зображення в меню](#вивід-зображення-в-меню)
    - [Підключення скриптів в адмінпанелі](#підключення-скриптів-в-адмінпанелі)
  - [Джерела](#джерела-1)
- [Документація TailwindNavWalker](#документація-tailwindnavwalker)
  - [Зміст](#зміст-1)
  - [Огляд класу](#огляд-класу-1)
  - [Конструктор](#конструктор)
  - [Метод start\_lvl](#метод-start_lvl)
  - [Метод end\_lvl](#метод-end_lvl)
  - [Метод start\_el](#метод-start_el)
    - [Основні деталі:](#основні-деталі)
  - [Пояснення синтаксису `@attrs`](#пояснення-синтаксису-attrs)
  - [Джерела](#джерела-2)

---

## Реєстрація активів теми

Код використовує функції WordPress для підключення скриптів та стилів через хук **wp_enqueue_scripts**:
- **wp_enqueue_scripts** – реєструє основні активи теми:
  ```php
  add_action('wp_enqueue_scripts', function () {
      bundle('app')->enqueue();
  }, 100);
  ```
  Використовується функція `bundle` (з пакету Roots) для підключення активів.

- **admin_enqueue_scripts** – підключає активи для адмінпанелі, зокрема для медіа-завантажувача:
  ```php
  add_action('admin_enqueue_scripts', function () {
      bundle('admin-media-uploader')->enqueue();
  }, 100);
  ```

- **enqueue_block_editor_assets** – завантажує скрипти та стилі для блокового редактора (Gutenberg):
  ```php
  add_action('enqueue_block_editor_assets', function () {
      bundle('editor')->enqueue();
  }, 100);
  ```

Докладніше про ці хуки можна прочитати на [developer.wordpress.org](https://developer.wordpress.org/themes/basics/).

---

## Налаштування теми

За допомогою хука **after_setup_theme** здійснюється первинне налаштування теми:
- **Відключення підтримки блокових шаблонів**:
  ```php
  remove_theme_support('block-templates');
  ```
  Це дозволяє відмовитись від функціоналу повноекранного редагування.

- **Реєстрація меню навігації**:
  ```php
  register_nav_menus([
      'primary_navigation' => __('Primary Navigation', 'sage'),
  ]);
  ```
  Посилання: [register_nav_menus](https://developer.wordpress.org/reference/functions/register_nav_menus/)

- **Відключення стандартних блокових шаблонів**:
  ```php
  remove_theme_support('core-block-patterns');
  ```
  Докладніше: [remove_theme_support](https://developer.wordpress.org/reference/functions/remove_theme_support/)

- **Додавання підтримки заголовка, мініатюр, HTML5 розмітки, адаптивних вставок та відновлюваних віджетів**:
  ```php
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');
  add_theme_support('responsive-embeds');
  add_theme_support('html5', [
      'caption',
      'comment-form',
      'comment-list',
      'gallery',
      'search-form',
      'script',
      'style',
  ]);
  add_theme_support('customize-selective-refresh-widgets');
  ```
  Докладніше про функцію `add_theme_support` – [WordPress Theme Support](https://developer.wordpress.org/reference/functions/add_theme_support/).

---

## Підтримка кастомного логотипа та заголовного зображення

### Кастомний логотип

Активується підтримка кастомного логотипа з можливістю налаштування розмірів:
```php
add_theme_support('custom-logo', [
    'height'      => 48,
    'width'       => 120,
    'flex-height' => true,
    'flex-width'  => true,
]);
```

### Заголовне зображення

За допомогою хука **customize_register** додається можливість налаштування заголовного зображення через Customizer:
- Створюється новий розділ `header_image_section` для фону заголовка.
- Додаються налаштування та контролери для завантаження зображень:
  ```php
  add_action('customize_register', function (\WP_Customize_Manager $wp_customize) {
      $wp_customize->add_section('header_image_section', [
          'title'    => __('Header Background', 'sage'),
          'priority' => 30,
      ]);
  
      $wp_customize->add_setting('header_background_image', [
          'default'           => '',
          'sanitize_callback' => 'esc_url_raw'
      ]);
  
      $wp_customize->add_control(new \WP_Customize_Image_Control($wp_customize, 'header_background_image', [
          'label'    => __('Header Background Image', 'sage'),
          'section'  => 'header_image_section',
          'settings' => 'header_background_image'
      ]));
  
      $wp_customize->add_setting('header_image');
      $wp_customize->add_control(new \WP_Customize_Image_Control(
          $wp_customize,
          'header_image',
          [
              'label'   => __('Header Image', 'sage'),
              'section' => 'title_tagline'
          ]
      ));
  });
  ```
  Додаткову інформацію можна знайти на [WP Customizer API](https://developer.wordpress.org/themes/customize-api/).

---

## Реєстрація віджетів

За допомогою хука **widgets_init** реєструються бокові панелі:
- Створюється базова конфігурація віджетів:
  ```php
  $config = [
      'before_widget' => '<section class="widget %1$s %2$s">',
      'after_widget'  => '</section>',
      'before_title'  => '<h3>',
      'after_title'   => '</h3>',
  ];
  ```
- Реєструються бокова панель **Primary** та **Footer**:
  ```php
  register_sidebar([
      'name' => __('Primary', 'sage'),
      'id'   => 'sidebar-primary',
  ] + $config);

  register_sidebar([
      'name' => __('Footer', 'sage'),
      'id'   => 'sidebar-footer',
  ] + $config);
  ```
  Докладніше: [widgets_init](https://developer.wordpress.org/reference/hooks/widgets_init/)

---

## Ініціалізація ModernImageHandler

За допомогою наступного коду створюється екземпляр класу **ModernImageHandler**:
```php
add_action('after_setup_theme', function() {
    new ModernImageHandler();
});
```
Цей клас, ймовірно, відповідає за сучасну обробку зображень у темі.

---

## Blade директива `modernPicture`

Реєструється користувацька директива Blade для відображення зображень:
```php
Blade::directive('modernPicture', function ($expression) {
    require_once get_theme_file_path('/app/moder-images.php');
    return "<?php echo \\App\\modern_picture($expression); ?>";
});
```
- Директива `modernPicture` підключає файл з функцією обробки зображень і повертає результат виконання функції `modern_picture`.
- Це дозволяє використовувати директиву безпосередньо у шаблонах Blade для відображення оптимізованих зображень.

---

## Джерела

- [register_nav_menus](https://developer.wordpress.org/reference/functions/register_nav_menus/)
- [add_theme_support](https://developer.wordpress.org/reference/functions/add_theme_support/)
- [remove_theme_support](https://developer.wordpress.org/reference/functions/remove_theme_support/)
- [wp_enqueue_scripts](https://developer.wordpress.org/reference/hooks/wp_enqueue_scripts/)
- [admin_enqueue_scripts](https://developer.wordpress.org/reference/hooks/admin_enqueue_scripts/)
- [enqueue_block_editor_assets](https://developer.wordpress.org/reference/hooks/enqueue_block_editor_assets/)
- [widgets_init](https://developer.wordpress.org/reference/hooks/widgets_init/)
- [Customizer API](https://developer.wordpress.org/themes/customize-api/)

---

# Документація функції asset_path

Функція `asset_path` визначає URL-адресу для заданого ассета (файлу) в темі WordPress, використовуючи файл манифесту для отримання оптимізованого шляху. Якщо файл манифесту відсутній або не містить запису для потрібного файлу, функція повертає базовий шлях до ассета.

---

## Алгоритм роботи

1. **Визначення шляху до файлу манифесту**  
   Функція формує повний шлях до файлу `manifest.json`, який знаходиться в директорії `/public` теми:
   ```php
   $public_path = get_stylesheet_directory() . '/public/manifest.json';
   ```

2. **Перевірка існування файлу манифесту**  
   Якщо файл `manifest.json` не існує, функція повертає прямий URL до ассета:
   ```php
   if (! file_exists($public_path)) {
       return get_stylesheet_directory_uri() . '/public/' . $asset;
   }
   ```

3. **Завантаження та декодування манифесту**  
   Якщо файл існує, його вміст завантажується і декодується у асоціативний масив:
   ```php
   $manifest = json_decode(file_get_contents($public_path), true);
   ```

4. **Пошук запису для ассета**  
   Якщо манифест містить запис для заданого файлу, повертається URL з використанням оптимізованого шляху:
   ```php
   if (isset($manifest[$asset])) {
       return get_stylesheet_directory_uri() . '/public/' . $manifest[$asset];
   }
   ```

5. **Повернення базового шляху**  
   Якщо запису немає, функція повертає базовий шлях до ассета:
   ```php
   return get_stylesheet_directory_uri() . '/public/' . $asset;
   ```

---

## Параметри

- **$asset** (`string`): Назва файлу ассета, для якого необхідно отримати URL.

---

## Повертає

- **string**: Повний URL до ассета, який формується на основі директорії теми та даних з файлу `manifest.json` (якщо він існує).

---

## Приклад використання

```php
$script_url = asset_path('js/app.js');
// Якщо у файлі manifest.json є запис для 'js/app.js',
// повернеться оптимізований шлях, інакше – базовий URL до файлу.
```

---

## Важливі моменти

- **Перевірка існування функції**:  
  Функція оголошується лише якщо вона ще не визначена, що дозволяє уникнути конфліктів:
  ```php
  if (! function_exists('asset_path')) { ... }
  ```
  
- **Гнучкість завдяки манифесту**:  
  Використання `manifest.json` дозволяє легко оновлювати шляхи до файлів, наприклад, при використанні системи збірки або кешування.

---
# Документація ThemeServiceProvider

Цей клас розширює базовий сервіс-провайдер від Sage та відповідає за додаткову кастомізацію теми. Він впроваджує функціонал для додавання зображень до пунктів меню, інтеграцію медіа-завантажувача та інші кастомні налаштування.

---



## Огляд класу

Клас `ThemeServiceProvider` знаходиться у просторі імен `App\Providers` і розширює базовий клас `SageServiceProvider` з Roots Acorn. Він містить два основних методи:
- `register()` – для реєстрації сервісів.
- `boot()` – для ініціалізації та підключення кастомних функцій теми.

---

## Метод register

Метод `register()` викликає батьківський метод для реєстрації всіх необхідних сервісів. Він не містить додаткової логіки, що дозволяє зберегти стандартний функціонал базового провайдера.

```php
public function register()
{
    parent::register();
}
```

---

## Метод boot

Метод `boot()` відповідає за завантаження та ініціалізацію кастомних налаштувань теми. Він містить кілька частин:

### Додавання поля зображення до пунктів меню

За допомогою фільтра `wp_setup_nav_menu_item` до кожного об’єкта меню додається властивість `menu_image`, яке отримується з мета-даних (`_menu_image`):

```php
add_filter('wp_setup_nav_menu_item', function ($item) {
    $item->menu_image = get_post_meta($item->ID, '_menu_image', true);
    return $item;
});
```

### Відображення кастомних полів у меню

За допомогою дії `wp_nav_menu_item_custom_fields` в адмінпанелі додається HTML-розмітка, яка включає текстове поле для URL зображення та кнопку для виклику медіа-завантажувача.  
Перед виведенням підключається допоміжний файл, а також визначається шлях до скрипта через функцію `asset_path`:

```php
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
    <!-- JavaScript буде описано нижче -->
    <?php
}, 10, 4);
```

### JavaScript для завантажувача зображень

У HTML-розмітці вбудовано скрипт, який:
- Додає обробник подій до кнопки.
- Відкриває WP медіа-фрейм для завантаження або вибору зображення.
- Після вибору зображення встановлює URL у відповідне текстове поле.

```html
<script>
    (() => {
        const uploadButton = document.getElementById("<?php echo $item_id; ?>")
        if(uploadButton){
            uploadButton.addEventListener('click', (e) => {
                e.preventDefault();
                console.log('click button id:', <?php echo $item_id; ?>)
                const input = uploadButton.parentElement.querySelector('.menu-item-image');
                if (uploadButton.myMediaFrame) {
                    uploadButton.myMediaFrame.open();
                    return;
                }
                const frame = wp.media({
                    title: 'Select or Upload Image',
                    button: {
                        text: 'Use this image'
                    },
                    multiple: false
                });
                frame.on('select', function() {
                    const attachment = frame.state().get('selection').first().toJSON();
                    input.value = attachment.url;
                });
                uploadButton.myMediaFrame = frame;
                frame.open();
            });
        }
    })()
</script>
```

### Збереження URL зображення

За допомогою дії `save_post` при збереженні меню перевіряється наявність переданих даних для зображень. Для кожного пункту меню виконується оновлення мета-даних з ключем `_menu_image`:

```php
add_action('save_post', function ($post_id) {
    if (isset($_POST['menu-item-image'])) {
        foreach ($_POST['menu-item-image'] as $item_id => $image_url) {
            update_post_meta($item_id, '_menu_image', sanitize_text_field($image_url));
        }
    }
});
```

### Вивід зображення в меню

Фільтр `walker_nav_menu_start_el` додає HTML-розмітку зображення до вихідного HTML пункту меню, якщо для нього заданий URL зображення та він не є верхнім рівнем:

```php
add_filter('walker_nav_menu_start_el', function ($item_output, $item, $depth, $args) {
    if (!empty($item->menu_image) && $item->menu_item_parent != 0) {
        $image_html = sprintf(
            '<img src="%s" alt="%s" class=" menu-item-image group-hover/hasparent:inline-block absolute left-full hidden" />',
            esc_url($item->menu_image),
            esc_attr($item->title)
        );
        $item_output .= $image_html;
    }
    return $item_output;
}, 10, 4);
```

### Підключення скриптів в адмінпанелі

У дії `admin_enqueue_scripts` перевіряється, чи знаходиться користувач на сторінці редагування меню. Якщо так – підключається медіа-бібліотека WordPress для забезпечення роботи завантажувача, а також логуються шляхи до скриптів:

```php
add_action('admin_enqueue_scripts', function() {
    require_once get_stylesheet_directory() . '/app/helpers.php';
    $script_path = asset_path('admin-media-uploader.js');
    error_log('Asset path: ' . $script_path); // Записуємо шлях у лог
    if (get_current_screen()->base === 'nav-menus') {
        wp_enqueue_media();
    }
});
```

Після виконання всіх кастомних дій викликається метод `parent::boot()` для завершення ініціалізації сервісів.

---

## Джерела

- [WordPress - add_filter](https://developer.wordpress.org/reference/functions/add_filter/)
- [WordPress - add_action](https://developer.wordpress.org/reference/functions/add_action/)
- [WordPress - wp.media](https://developer.wordpress.org/reference/functions/wp_media/)
- [WordPress - update_post_meta](https://developer.wordpress.org/reference/functions/update_post_meta/)
- [Roots Acorn](https://roots.io/acorn/)

---

# Документація TailwindNavWalker

Клас `TailwindNavWalker` розширює стандартний клас `\Walker_Nav_Menu` WordPress для генерації HTML розмітки навігаційного меню із використанням Tailwind CSS. Він підтримує як десктопний, так і мобільний режими відображення завдяки параметру `$isMobile`.

---

## Зміст
- [Огляд класу](#огляд-класу)
- [Конструктор](#конструктор)
- [Метод start_lvl](#метод-start_lvl)
- [Метод end_lvl](#метод-end_lvl)
- [Метод start_el](#метод-start_el)
- [Пояснення синтаксису `@attrs`](#пояснення-синтаксису-attrs)
- [Джерела](#джерела)

---

## Огляд класу

Клас знаходиться в просторі імен `App\Walkers` і призначений для кастомізації HTML розмітки меню. Основні можливості:
- Генерація контейнерів для підменю (вкладених списків) із різними класами для мобільних та десктопних пристроїв.
- Динамічне формування класів та атрибутів для елементів меню.
- Додавання додаткової SVG-іконки для пунктів меню, що містять підменю.

---

## Конструктор

```php
public function __construct($isMobile = false) {
    $this->isMobile = $isMobile;
}
```

Конструктор приймає один параметр `$isMobile`, який визначає режим відображення меню:
- Якщо `$isMobile` дорівнює `true`, використовується мобільна версія класів.
- За замовчуванням – десктопний режим.

---

## Метод start_lvl

Метод `start_lvl` відповідає за створення початку рівня вкладеного меню. Він генерує HTML-контейнер, що складається з `<div>` і `<ul>`, з класами, залежно від того, чи меню мобільне чи десктопне.

```php
public function start_lvl(&$output, $depth = 0, $args = null) {
    $classes = $this->isMobile
        ? 'submenu-wrapper hidden w-full bg-white  z-50' // Мобільний режим
        : 'submenu-wrapper hidden group-hover:block absolute top-0 left-full w-48 bg-white  z-50 ml-0'; // Десктопний режим

    $output .= '<!-- Start div output start_lvl -->
                <div class="' . esc_attr($classes) . '">
                    <!-- Start ul output start_lvl -->
                    <ul data-qaid="walker-nav-menu-start-lvl" class="relative">';
}
```

- **Мобільний режим**: використовується клас `submenu-wrapper hidden w-full bg-white  z-50`.
- **Десктопний режим**: використовується клас `submenu-wrapper hidden group-hover:block absolute top-0 left-full w-48 bg-white  z-50 ml-0`.

---

## Метод end_lvl

Метод `end_lvl` завершує HTML-структуру вкладеного меню, закриваючи теги `<ul>` і `<div>`.

```php
public function end_lvl(&$output, $depth = 0, $args = null) {
    $output .= '</ul></div>';
}
```

---

## Метод start_el

Метод `start_el` генерує HTML для окремого елементу меню (`<li>`). Основні кроки:

1. **Отримання та модифікація класів**:
   - Отримуються класи елементу з об'єкта `$item`.
   - Визначається, чи має пункт меню підменю (`menu-item-has-children`) та чи є він дочірнім елементом.

2. **Формування CSS класів посилання**:
   - Залежно від того, чи має елемент підменю та режим відображення (мобільний або десктопний), формується набір класів для посилання.
   - Для мобільних елементів із підменю використовується клас `mobile-has-children-classes`, а для десктопу – `pc-has-children-classes`. Для елементів без підменю використовується клас `pc-without-children-classes`.

3. **Генерація HTML розмітки**:
   - Вихідний рядок починається з тегу `<li>` з додатковими атрибутами, такими як `data-path` (що містить URL пункту меню) та додатковими data-атрибутами для мобільного меню.
   - Всередині тегу `<li>` створюється тег `<span>`, який містить текст пункту меню та, якщо він має підменю, SVG-іконку.

```php
public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
    $classes = empty($item->classes) ? array() : (array) $item->classes;
    $has_children = in_array('menu-item-has-children', $classes);
    $hasparent = $item->menu_item_parent != 0;

    $mobileHasChildrenClasses = " mobile-has-children-classes hover:text-white flex items-center justify-between text-gray-800 transition-colors duration-200 w-full px-4 py-2";
    $pcHasChildrenClasses = " pc-has-children-classes  hover:text-white flex items-center justify-between text-gray-800 transition-colors duration-200  px-4 py-2 pointer-events-none";
    $pcWithoutChildrenClasses = " pc-without-children-classes hover:text-white text-gray-800 group-hover/hasparent:text-white transition-colors duration-200 block  px-4 py-2";

    if ($has_children) {
        $classes[] = $this->isMobile ? 'mobile-parent' : 'group';
    }
    
    $classes[] = 'hover:bg-[#BA2C73] hover:text-white cursor-pointer';
    
    $class_names_has_parent = $hasparent ? 'group/hasparent ' : 'without-parent ';
    $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
    $class_names = $class_names 
        ? ' class="' . $class_names_has_parent . esc_attr($class_names) . ' relative flex flex-col"' 
        : ' class="relative flex"';

    $data_attrs = $this->isMobile && $has_children 
        ? ' data-mobile-menu="true"' 
        : '';
    $children_data_attrs = $this->isMobile && $hasparent
        ? ' data-mobile-menu-child="true"' 
        : '' ;

    $output .= '
    <!-- li menu element -->
    <li' . ' data-path="' . esc_attr($item->url) . '"' . $class_names . $data_attrs . $children_data_attrs .'>';

    $link_classes = $has_children 
        ? (
            $this->isMobile 
            ?  $mobileHasChildrenClasses 
            :  $pcHasChildrenClasses
        )
        : $pcWithoutChildrenClasses;

    $item_output = $args->before;
    $item_output .= '<span class="' . $link_classes . '">';
    $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;

    if ($has_children) {
        $item_output .= '<svg class="w-4 h-4 ml-0 " fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>';
    }

    $item_output .= '</span>';
    $item_output .= $args->after;

    $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
}
```

### Основні деталі:
- **Динамічне додавання класів**: Використання фільтра `nav_menu_css_class` дозволяє змінювати набір класів для кожного пункту меню.
- **Data-атрибути**: Додаткові data-атрибути (`data-mobile-menu`, `data-mobile-menu-child`) використовуються для інтеграції JavaScript логіки, що забезпечує адаптивність меню.
- **SVG-іконка**: Додається у випадку, якщо пункт має підменю, що сигналізує користувачу про наявність додаткових рівнів навігації.

---

## Пояснення синтаксису `@attrs`

У кінці файлу наведено коментар, який пояснює принцип роботи з атрибутами елементів меню:
- **Атрибути** формуються на основі властивостей об'єкта `$item`:
  - `attr_title` – для підказки (атрибут `title`).
  - `target` – визначає, де відкриватиметься посилання.
  - `xfn` – значення для атрибуту `rel`, наприклад, `nofollow` чи `noopener`.
  - `url` – URL, на який веде посилання.
- Функція `esc_attr()` використовується для безпечного виведення значень.
- Позначення `@attrs` є узагальненим терміном для всіх атрибутів, що додаються до елемента, зокрема до тегу `<a>`.

Це пояснення допомагає розробникам зрозуміти, як відбувається формування HTML атрибутів при генерації меню.

---

## Джерела

- [WordPress - Walker_Nav_Menu](https://developer.wordpress.org/reference/classes/walker_nav_menu/)
- [WordPress - apply_filters](https://developer.wordpress.org/reference/functions/apply_filters/)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)

  
  