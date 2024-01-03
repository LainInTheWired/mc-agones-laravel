<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                {{-- テキスト --}}
                <div class="mb-10 md:mb-16 mt-10">
                    <h2 class="mb-4 text-center text-2xl font-bold text-gray-800 md:mb-6 lg:text-3xl">サーバ作成</h2>
                    <p class="mx-auto max-w-screen-md text-center text-gray-500 md:text-lg">スペックやなんや感やいれてね<p>
                </div>
                {{-- フォーム --}}
                <form action="/create_server" class="mx-auto grid max-w-screen-md gap-4 sm:grid-cols-2" method="POST">
                  @csrf
                    <div class="sm:col-span-2">
                      <label for="company" class="mb-2 inline-block text-sm text-red-600 sm:text-base">サーバ名(必須)</label>
                      @error('server_name')
                        <p>{{$message}}</p>
                      @enderror
                      <input name="server_name" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" />
                    </div>
              
                    <div class="sm:col-span-2">
                      <label for="doamin" class="mb-2 inline-block text-sm text-red-600 sm:text-base">ドメイン(必須)</label>
                      @error('domain')
                        <p>{{$message}}</p>
                      @enderror
                      <input name="domain" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" />
                    </div>
              
                    <div class="sm:col-span-2">
                      <label for="subject" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">補足説明(任意)</label>
                      <input name="subject" class="w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring" />
                    </div>
              
                    <div class="sm:col-span-2">
                      <label for="message" class="mb-2 inline-block text-sm text-gray-800 sm:text-base">オプション</label>
                      <textarea name="message" class="h-64 w-full rounded border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring"></textarea>
                    </div>
              
                    <div class="flex items-center justify-between sm:col-span-2">
                      <button class="inline-block rounded-lg bg-indigo-500 px-8 py-3 text-center text-sm font-semibold text-white outline-none ring-indigo-300 transition duration-100 hover:bg-indigo-600 focus-visible:ring active:bg-indigo-700 md:text-base">Send</button>
                      <span class="text-sm text-gray-500">*Required</span>
                    </div>
                    <p class="text-xs text-gray-400 mb-10">By signing up to our newsletter you agree to our <a href="#" class="underline transition duration-100 hover:text-indigo-500 active:text-indigo-600">Privacy Policy</a>.</p>
                  </form>
            </div>
        </div>
    </div>
</x-app-layout>
