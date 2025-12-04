@if(config('cine-reserve.show_movie_information', true))
<div class="bg-white dark:bg-[#08080a] rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-800">
    <div class="flex items-center gap-6">
        <div class="w-32 h-48 bg-gray-300 dark:bg-gray-600 rounded-lg"></div>
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Movie Title</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-4">Genre • Duration • Rating</p>
            <div class="space-y-2">
                <p class="text-sm text-gray-700 dark:text-gray-300"><span class="font-semibold">Date:</span> January 1, 2024</p>
                <p class="text-sm text-gray-700 dark:text-gray-300"><span class="font-semibold">Time:</span> 7:00 PM</p>
                <p class="text-sm text-gray-700 dark:text-gray-300"><span class="font-semibold">Theater:</span> Screen 1</p>
            </div>
        </div>
    </div>
</div>
@endif