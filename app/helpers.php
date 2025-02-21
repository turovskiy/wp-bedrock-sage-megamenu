<?php

if (! function_exists('asset_path')) {
    function asset_path($asset) {
        $public_path = get_stylesheet_directory() . '/public/manifest.json';
        
        // Якщо файл манифесту не існує, повертаємо прямий шлях до ассета
        if (! file_exists($public_path)) {
            return get_stylesheet_directory_uri() . '/public/' . $asset;
        }
        
        $manifest = json_decode(file_get_contents($public_path), true);
        
        // Якщо у манифесті є запис для потрібного файлу, повертаємо його шлях
        if (isset($manifest[$asset])) {
            return get_stylesheet_directory_uri() . '/public/' . $manifest[$asset];
        }
        
        // Інакше повертаємо базовий шлях
        return get_stylesheet_directory_uri() . '/public/' . $asset;
    }
}