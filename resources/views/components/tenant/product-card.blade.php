@props(['product'])

<div class="group bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-xl overflow-hidden hover:border-neutral-300 dark:hover:border-neutral-600 hover:shadow-lg dark:hover:shadow-neutral-900/50 transition-all duration-300">

    <div class="relative aspect-[4/3] overflow-hidden bg-neutral-100 dark:bg-neutral-800">
        <div class="absolute inset-0 bg-cover bg-center group-hover:scale-105 transition-transform duration-700"
             style="background-image: url('https://picsum.photos/400/300?random={{ $product }}');">
        </div>

        <button class="absolute top-3 right-3 w-9 h-9 bg-white/90 dark:bg-neutral-900/80 backdrop-blur rounded-full flex items-center justify-center text-neutral-400 hover:text-red-500 transition-colors shadow-sm border border-white/20">
            <i class="fa-regular fa-heart"></i>
        </button>

        <span class="absolute top-3 left-3 bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 text-[10px] uppercase font-bold px-2.5 py-1 rounded-full shadow-md">
            New
        </span>
    </div>

    <div class="p-5">
        <div class="mb-2">
            <span class="text-xs text-neutral-500 dark:text-neutral-400 font-bold uppercase tracking-wider">Category</span>
        </div>
        <h3 class="font-bold text-neutral-900 dark:text-white text-lg mb-2 line-clamp-1 group-hover:text-neutral-600 dark:group-hover:text-neutral-300 transition-colors">
            Premium Product {{ $product }}
        </h3>
        <p class="text-sm text-neutral-500 dark:text-neutral-400 line-clamp-2 mb-4">
            Experience quality like never before with this amazing item designed for you.
        </p>

        <div class="pt-4 border-t border-neutral-100 dark:border-neutral-800 flex items-center justify-between">
            <div class="flex flex-col">
                <span class="text-xs text-neutral-400 dark:text-neutral-600 line-through font-medium">${{ rand(100, 199) }}.00</span>
                <span class="text-lg font-bold text-neutral-900 dark:text-white">${{ rand(10, 99) }}.99</span>
            </div>
            <button class="px-5 py-2.5 bg-neutral-900 hover:bg-neutral-800 dark:bg-white dark:hover:bg-neutral-200 text-white dark:text-neutral-900 text-sm font-semibold rounded-lg transition-transform active:scale-95">
                Add to Cart
            </button>
        </div>
    </div>
</div>
