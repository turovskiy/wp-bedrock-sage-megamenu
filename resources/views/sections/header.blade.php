
<header class="flex min-w-full flex-col border-2 border-solid border-[#4D4E7C] bg-[#201E50]  md:flex-row md:items-center md:justify-between">
  <div class="h-auto py-4 md:min-w-full md:py-[14px] lg:px-[120px]">
    <div class="flex flex-col px-4">
      <div class="flex min-w-full flex-col items-start gap-4 ">
        <div class="flex min-w-full flex-row items-center justify-between">

          <!-- <img width="238" height="92" src="http://root.test/app/uploads/2025/02/cropped-alogo-4-1-1.png" class="h-10 w-auto" alt="Logo" /> -->
          {{-- Вивід логотипа --}}
            @if (get_theme_mod('custom_logo'))
              @modernPicture(get_theme_mod('custom_logo'), 'full', ['class' => 'max-w-[120px] max-h-[50px]'])
            @else
              <a href="{{ home_url('/') }}" class="text-xl font-bold">
                {{ get_bloginfo('name', 'display') }}
              </a>
            @endif
          <search class="hidden pt-2 md:contents">
            <form action="./search/" class="md:flex md:w-full md:max-w-full md:gap-2 md:px-4">
              <label for="pc-search" class="sr-only">Search</label>
              <input type="search" id="pc-search" name="q" class="h-11 w-full px-2" placeholder="Search..." />
              <button type="submit" id="pc-submit-search" class="rounded-md border-[1px] border-[#4D4E7C] px-3 text-white  hover:bg-[#BA2C73]">Search</button>
            </form>
          </search>
          <button id="login" class="">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-8 cursor-pointer stroke-white md:pr-2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
            </svg>
          </button>
          <button class="flex h-[50px] items-center justify-center gap-2 rounded-sm bg-[#BA2C73] px-2 text-white">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 stroke-white">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
            </svg>
            <span>£0.00</span>
          </button>
        </div>
      </div>
    </div>
  </div>
  <search class="search-modile pt-2 md:hidden">
    <form action="./search/" class="mb-4 flex w-full gap-2 px-4">
      <label for="mobile-search" class="sr-only">Search</label>
      <input type="search" id="mobile-search" name="q" class="h-11 w-full px-2" placeholder="Search..." />
      <button type="submit" id="mobile-submit-search" class="rounded-md border-[1px] border-[#4D4E7C] px-3 text-white shadow-lg hover:bg-[#BA2C73]">Search</button>
    </form>
  </search>
</header>

<div class="w-full bg-[#201E50]">
  <button data-qaid="show-menu-pc" id="show-menu-pc" class="flex items-center justify-center gap-2 pl-[10vw] md:pl-[17vw] text-white py-2">
        <svg width="15" height="13" viewBox="0 0 15 13" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M2.34375 0.65625C2.57812 0.65625 2.8125 0.890625 2.8125 1.125V3C2.8125 3.26367 2.57812 3.46875 2.34375 3.46875H0.46875C0.205078 3.46875 0 3.26367 0 3V1.125C0 0.890625 0.205078 0.65625 0.46875 0.65625H2.34375ZM2.34375 5.34375C2.57812 5.34375 2.8125 5.57812 2.8125 5.8125V7.6875C2.8125 7.95117 2.57812 8.15625 2.34375 8.15625H0.46875C0.205078 8.15625 0 7.95117 0 7.6875V5.8125C0 5.57812 0.205078 5.34375 0.46875 5.34375H2.34375ZM2.34375 10.0312C2.57812 10.0312 2.8125 10.2656 2.8125 10.5V12.375C2.8125 12.6387 2.57812 12.8438 2.34375 12.8438H0.46875C0.205078 12.8438 0 12.6387 0 12.375V10.5C0 10.2656 0.205078 10.0312 0.46875 10.0312H2.34375ZM14.5312 6.04688C14.7656 6.04688 15 6.28125 15 6.51562V6.98438C15 7.24805 14.7656 7.45312 14.5312 7.45312H5.15625C4.89258 7.45312 4.6875 7.24805 4.6875 6.98438V6.51562C4.6875 6.28125 4.89258 6.04688 5.15625 6.04688H14.5312ZM14.5312 10.7344C14.7656 10.7344 15 10.9688 15 11.2031V11.6719C15 11.9355 14.7656 12.1406 14.5312 12.1406H5.15625C4.89258 12.1406 4.6875 11.9355 4.6875 11.6719V11.2031C4.6875 10.9688 4.89258 10.7344 5.15625 10.7344H14.5312ZM14.5312 1.35938C14.7656 1.35938 15 1.59375 15 1.82812V2.29688C15 2.56055 14.7656 2.76562 14.5312 2.76562H5.15625C4.89258 2.76562 4.6875 2.56055 4.6875 2.29688V1.82812C4.6875 1.59375 4.89258 1.35938 5.15625 1.35938H14.5312Z" fill="white" />
        </svg>
        <span>Products</span>
  </button>
</div>

<div class="relative min-w-full ">
    @php
      $isMobile = wp_is_mobile();
    @endphp
      <div data-qaid="menu-bg" class="absolute bg-white w-full min-h-72 top-0 hidden">
          <nav id="nav-{{ $isMobile ? 'mobile' : 'pc' }}" 
              class="{{ 
              $isMobile 
              ? 'left-0 top-full z-50 hidden bg-white  group-hover:block' 
              : 'left-0 top-full z-50 hidden h-max  pl-[17vw] group-hover:block' 
              }}">
            <div class="flex flex-col gap-4">
            @if (has_nav_menu('primary_navigation'))
                    {!! wp_nav_menu([
                      'theme_location' => 'primary_navigation',
                      'menu_class' => 'nav-primary',
                      'container' => false,
                      'walker' => new \App\Walkers\TailwindNavWalker($isMobile)
                    ]) !!}
              @endif
            </div>
          </nav>
      </div>
      @if($isMobile)
        <script>
          /*
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuItems = document.querySelectorAll('[data-mobile-menu="true"]');
            
            mobileMenuItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    const submenu = this.querySelector('.submenu-wrapper');
                    console.log('submenu', submenu)
                    console.log('item', item)
                    console.log('target', e.target)
                    if (submenu) {
                        // Toggle submenu visibility
                        submenu.classList.toggle('hidden');
                        
                        // Toggle arrow rotation
                        const arrow = this.querySelector('svg');
                        if (arrow) {
                            arrow.style.transform = submenu.classList.contains('hidden') 
                                ? 'rotate(0deg)' 
                                : 'rotate(90deg)';
                        }
                    }
                });
            });
        });
        */
        </script>
      @endif
</div>