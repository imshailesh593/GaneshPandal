<x-layouts.app title="Complete your profile">
    <div class="rounded-xl border-t-4 border-gold-300 bg-white p-6 shadow-md">
        <h1 class="font-display text-2xl text-maroon-950">स्वागत आहे!</h1>
        <p class="mt-1 text-sm text-maroon-900/60">
            आम्ही <span class="font-medium text-maroon-900/80">{{ $mobile }}</span> व्हेरिफाय केला.
            अकाउंट पूर्ण करण्यासाठी थोडी माहिती द्या.
        </p>

        @if ($errors->any())
            <div class="mt-4 rounded-md bg-red-50 px-3 py-2 text-sm text-red-700">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('auth.complete-profile') }}" class="mt-4 space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-maroon-900/80">तुमचं नाव</label>
                <input id="name" name="name" type="text" required value="{{ old('name') }}"
                       class="mt-1 w-full rounded-md border border-maroon-950/15 px-3 py-2 text-base focus:border-vermillion-600 focus:outline-none">
            </div>

            <div>
                <label for="family_name" class="block text-sm font-medium text-maroon-900/80">कुटुंब / घराचं नाव</label>
                <p class="text-xs text-maroon-900/50">उदा. तुमचा घर नंबर किंवा आडनाव. एका कुटुंबातल्या सगळ्यांची
                    आरती बुकिंग एकच असते.</p>
                <input id="family_name" name="family_name" type="text" required value="{{ old('family_name') }}"
                       class="mt-1 w-full rounded-md border border-maroon-950/15 px-3 py-2 text-base focus:border-vermillion-600 focus:outline-none">
            </div>

            <button type="submit"
                    class="w-full rounded-md bg-vermillion-600 px-4 py-2 font-semibold text-white hover:bg-vermillion-700">
                अकाउंट तयार करा
            </button>
        </form>
    </div>
</x-layouts.app>
