<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>マイクラサーバを作ろう</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    @vite('resources/css/app.css')
</head>
<body>
    <!-- hero - start -->
<div class="bg-white pb-6 sm:pb-8 lg:pb-12">
    <div class="mx-auto max-w-screen-2xl px-4 md:px-8">
      <header class="mb-8 flex items-center justify-between py-4 md:mb-12 md:py-8 xl:mb-16">
        <!-- logo - start -->
        <a href="/" class="inline-flex items-center gap-2.5 text-2xl font-bold text-black md:text-3xl" aria-label="logo">
            m8s
       </a>
        <!-- logo - end -->
  
        <!-- nav - start -->
        <nav class=" gap-12 lg:flex">
          <a href="{{ route('login') }}" class="text-lg font-semibold text-gray-600 transition duration-100 hover:text-indigo-500 active:text-indigo-700">login</a>
          <a href="{{ route('register') }}" class="text-lg font-semibold text-gray-600 transition duration-100 hover:text-indigo-500 active:text-indigo-700 ml-9	">register</a>
        </nav>
        <!-- nav - end -->
  
        <!-- buttons - start -->
{{--   
        <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-gray-200 px-2.5 py-2 text-sm font-semibold text-gray-500 ring-indigo-300 hover:bg-gray-300 focus-visible:ring active:text-gray-700 md:text-base lg:hidden">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
          </svg>
  
          Menu
        </button> --}}
        <!-- buttons - end -->
      </header>
  
      <section class="flex flex-col justify-between gap-6 sm:gap-10 md:gap-16 lg:flex-row">
        <!-- content - start -->
        <div class="flex flex-col justify-center sm:text-center lg:py-12 lg:text-left xl:w-5/12 xl:py-24">
          <p class="mb-4 font-semibold text-indigo-500 md:mb-6 md:text-lg xl:text-xl">マイクラサーバを簡単に作ろう</p>
  
          <h1 class="mb-8 text-4xl font-bold text-black sm:text-5xl md:mb-12 md:text-6xl">現在テスト中です。使用しないでください。普通に消えますよ！</h1>
  
          <p class="mb-8 leading-relaxed text-gray-500 md:mb-12 lg:w-4/5 xl:text-lg"</p>
  
          <div class="flex flex-col gap-2.5 sm:flex-row sm:justify-center lg:justify-start">
            <a href="#" class="inline-block rounded-lg bg-indigo-500 px-8 py-3 text-center text-sm font-semibold text-white outline-none ring-indigo-300 transition duration-100 hover:bg-indigo-600 focus-visible:ring active:bg-indigo-700 md:text-base">始めよう</a>
  
            <a href="#" class="inline-block rounded-lg bg-gray-200 px-8 py-3 text-center text-sm font-semibold text-gray-500 outline-none ring-indigo-300 transition duration-100 hover:bg-gray-300 focus-visible:ring active:text-gray-700 md:text-base">ガイド</a>
          </div>
        </div>
        <!-- content - end -->
  
        <!-- image - start -->
        <div class="h-48 overflow-hidden rounded-lg bg-gray-100 shadow-lg lg:h-auto xl:w-5/12">
          <img src="https://images.unsplash.com/photo-1618004912476-29818d81ae2e?auto=format&q=75&fit=crop&w=1000" loading="lazy" alt="Photo by Fakurian Design" class="h-full w-full object-cover object-center" />
        </div>
        <!-- image - end -->
      </section>
    </div>
  </div>
  <!-- hero - end -->
  
  
  </div>
  <!-- footer - end -->
</body>
</html>