<x-error-layout>
    @section('title', '429 | Príliš veľa požiadaviek')
    <x-error-main-text
        error-code="429"
        title="Príliš veľa požiadaviek"
    >
        Spomaľte, prosím. Zaznamenali sme príliš veľa požiadaviek z vašej strany. Skúste to znova o <b>1 minútu</b>.
    </x-error-main-text>

    <x-error-buttons/>

    <x-error-bug-report/>
</x-error-layout>




