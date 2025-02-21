<?php

namespace App\Walkers;

class TailwindNavWalker extends \Walker_Nav_Menu {
    private $isMobile = false;

    public function __construct($isMobile = false) {
        $this->isMobile = $isMobile;
    }

    public function start_lvl(&$output, $depth = 0, $args = null) {

        $classes = $this->isMobile
            ? 'submenu-wrapper hidden w-full bg-white  z-50' // Mobile classes
            : 'submenu-wrapper hidden group-hover:block absolute top-0 left-full w-48 bg-white  z-50 ml-0'; // Desktop classes

        $output .= '<!-- Start div output start_lvl -->
                    <div class="' . esc_attr($classes) . '">
                        <!-- Start ul output start_lvl -->
                        <ul data-qaid="walker-nav-menu-start-lvl" class="relative">';
    }

    public function end_lvl(&$output, $depth = 0, $args = null) {
        $output .= '</ul></div>';
    }

    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $has_children = in_array('menu-item-has-children', $classes);
        $hasparent = $item->menu_item_parent != 0;

        //
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
            ? 
            (
                $this->isMobile 
                ?  $mobileHasChildrenClasses 
                :  $pcHasChildrenClasses
            )
            : 
            $pcWithoutChildrenClasses;

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
}

/**
 * Пояснення синтаксису '@attrs':
 *
 * У WordPress атрибути посилань у меню формуються на основі об'єкта $item, який містить інформацію про меню.
 * Атрибути передаються у вигляді рядкових значень, які додаються до HTML тегів.
 *
 * Наприклад, при формуванні тегу <a> використовується наступний код:
 *
 * $attributes .= !empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
 *
 * Тут використано '@attrs' як узагальнене позначення всіх атрибутів, які можуть бути додані до елемента.
 *
 * WordPress генерує ці атрибути за допомогою властивостей об'єкта $item:
 * - attr_title: використовується для додавання підказки (title) при наведенні миші.
 * - target: визначає, де відкриється посилання (наприклад, '_blank' для нового вікна).
 * - xfn: задає значення для rel атрибуту, наприклад 'nofollow' чи 'noopener'.
 * - url: задає посилання, за яким переходить користувач.
 *
 * Під час генерації меню WordPress використовує клас Walker_Nav_Menu, який викликає методи start_lvl, end_lvl та start_el для формування HTML структури.
 *
 * У процесі застосування атрибутів використовується функція esc_attr() для безпечного відображення значень атрибутів у HTML.
 *
 * Таким чином, синтаксис '@attrs' є загальним терміном для атрибутів, які передаються з $item в тег <a>.
 */

