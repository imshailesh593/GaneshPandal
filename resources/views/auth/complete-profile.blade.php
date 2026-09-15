<x-layouts.app title="Complete your profile">
    <div class="rounded-xl bg-white p-6 shadow-sm">
        <h1 class="text-xl font-semibold text-slate-900">स्वागत आहे!</h1>
        <p class="mt-1 text-sm text-slate-500">
            आम्ही <span class="font-medium text-slate-700">{{ $mobile }}</span> व्हेरिफाय केला.
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
                <label for="name" class="block text-sm font-medium text-slate-700">तुमचं नाव</label>
                <input id="name" name="name" type="text" required value="{{ old('name') }}"
                       class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-base focus:border-orange-500 focus:outline-none">
            </div>

            <div>
                <label for="family_name" class="block text-sm font-medium text-slate-700">कुटुंब / घराचं नाव</label>
                <p class="text-xs text-slate-500">उदा. तुमचा घर नंबर किंवा आडनाव. एका कुटुंबातल्या सगळ्यांची
                    आरती बुकिंग एकच असते.</p>
                <input id="family_name" name="family_name" type="text" required value="{{ old('family_name') }}"
                       class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-base focus:border-orange-500 focus:outline-none">
            </div>

            <button type="submit"
                    class="w-full rounded-md bg-orange-600 px-4 py-2 font-medium text-white hover:bg-orange-700">
                अकाउंट तयार करा
            </button>
        </form>
    </div>
</x-layouts.app>
