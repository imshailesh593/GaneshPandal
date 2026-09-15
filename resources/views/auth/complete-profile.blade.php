<x-layouts.app title="Complete your profile">
    <div class="rounded-xl bg-white p-6 shadow-sm">
        <h1 class="text-xl font-semibold text-slate-900">Welcome!</h1>
        <p class="mt-1 text-sm text-slate-500">
            We verified <span class="font-medium text-slate-700">{{ $mobile }}</span>.
            Tell us a bit about yourself to finish creating your account.
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
                <label for="name" class="block text-sm font-medium text-slate-700">Your name</label>
                <input id="name" name="name" type="text" required value="{{ old('name') }}"
                       class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-base focus:border-orange-500 focus:outline-none">
            </div>

            <div>
                <label for="family_name" class="block text-sm font-medium text-slate-700">Family / house name</label>
                <p class="text-xs text-slate-500">E.g. your house number or family surname. Members of the same
                    family share one aarti booking at a time.</p>
                <input id="family_name" name="family_name" type="text" required value="{{ old('family_name') }}"
                       class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-base focus:border-orange-500 focus:outline-none">
            </div>

            <button type="submit"
                    class="w-full rounded-md bg-orange-600 px-4 py-2 font-medium text-white hover:bg-orange-700">
                Create account
            </button>
        </form>
    </div>
</x-layouts.app>
