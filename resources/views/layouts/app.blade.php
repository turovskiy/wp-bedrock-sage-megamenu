<!doctype html>
<html @php(language_attributes())><!-- Вставка мовних атрибутів від WordPress через Blade -->
  
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php(do_action('get_header'))
    @php(wp_head())
  </head>

  <body @php(body_class())>
    @php(wp_body_open())

    <div id="app" class="min-h-screen overflow-y-auto overflow-x-hidden @container/app">
      <a class="sr-only focus:not-sr-only" href="#main">
        {{ __('Skip to content') }}
      </a>

      @include('sections.header')

      <main id="main" class="main @container/main-content prose lg:prose-xl">
        @yield('content')
      </main>

      @hasSection('sidebar')
        <aside class="sidebar">
          @yield('sidebar')
        </aside>
      @endif

      <!-- @include('sections.footer') -->
    </div>

    <!-- @php(do_action('get_footer')) -->
    @php(wp_footer())
  </body>
</html>
