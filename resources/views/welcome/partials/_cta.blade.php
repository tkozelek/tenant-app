<section class="py-24 bg-white dark:bg-gray-950">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto bg-indigo-600 rounded-3xl p-8 md:p-16 text-center text-white shadow-2xl shadow-indigo-200 dark:shadow-none relative overflow-hidden">
            <!-- Background Decoration -->
            <div class="absolute top-0 right-0 -mt-8 -mr-8 opacity-10">
                <i class="fa-solid fa-cube text-9xl"></i>
            </div>

            <h2 class="text-3xl md:text-5xl font-bold mb-6 relative z-10">
                Pripravený posunúť váš <br> biznis na vyššiu úroveň?
            </h2>
            <p class="text-indigo-100 text-lg mb-10 max-w-2xl mx-auto relative z-10">
                Pridajte sa k stovkám obchodníkov, ktorí už dnes predávajú inteligentnejšie. Registrácia trvá menej ako minútu.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center relative z-10">
                <a href="{{ route('register') }}" class="px-8 py-4 bg-white text-indigo-600 hover:bg-indigo-50 font-bold rounded-xl transition-all">
                    Vytvoriť bezplatný účet
                </a>
                <a href="{{ route('login') }}" class="px-8 py-4 bg-indigo-700 hover:bg-indigo-800 text-white font-bold rounded-xl transition-all">
                    Prihlásiť sa
                </a>
            </div>

            <div class="mt-8 flex items-center justify-center space-x-6 text-sm text-indigo-200 relative z-10">
                <span><i class="fa-solid fa-check mr-2"></i> Bez kreditnej karty</span>
                <span><i class="fa-solid fa-check mr-2"></i> Zrušenie kedykoľvek</span>
            </div>
        </div>
    </div>
</section>
