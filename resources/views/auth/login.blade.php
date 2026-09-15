<x-layouts.app title="Login">
    <div class="rounded-xl bg-white p-6 shadow-sm">
        <h1 class="text-xl font-semibold text-slate-900">मेंबर लॉगिन</h1>
        <p class="mt-1 text-sm text-slate-500">तुमचा मोबाईल नंबर टाका, आरती स्लॉट बुक करण्यासाठी किंवा महाप्रसाद व्हॉलंटियर करण्यासाठी.</p>

        <div id="auth-error" class="mt-4 hidden rounded-md bg-red-50 px-3 py-2 text-sm text-red-700"></div>

        <form id="phone-form" class="mt-4 space-y-3">
            <label class="block text-sm font-medium text-slate-700" for="mobile">मोबाईल नंबर</label>
            <div class="flex items-center gap-2">
                <span class="rounded-md bg-slate-100 px-3 py-2 text-sm text-slate-600">+91</span>
                <input id="mobile" type="tel" inputmode="numeric" maxlength="10" required
                       placeholder="98765 43210"
                       class="w-full rounded-md border border-slate-300 px-3 py-2 text-base focus:border-orange-500 focus:outline-none">
            </div>
            <button type="submit" id="send-otp-btn"
                    class="w-full rounded-md bg-orange-600 px-4 py-2 font-medium text-white hover:bg-orange-700 disabled:opacity-50">
                OTP पाठवा
            </button>
        </form>

        <form id="otp-form" class="mt-4 hidden space-y-3">
            <label class="block text-sm font-medium text-slate-700" for="otp">6 अंकी OTP टाका</label>
            <input id="otp" type="text" inputmode="numeric" maxlength="6" required
                   placeholder="000000"
                   class="w-full rounded-md border border-slate-300 px-3 py-2 text-center text-lg tracking-widest focus:border-orange-500 focus:outline-none">
            <button type="submit" id="verify-otp-btn"
                    class="w-full rounded-md bg-orange-600 px-4 py-2 font-medium text-white hover:bg-orange-700 disabled:opacity-50">
                OTP व्हेरिफाय करा
            </button>
            <button type="button" id="change-number-btn" class="w-full text-sm text-slate-500 underline">
                नंबर बदला
            </button>
        </form>

        <div id="recaptcha-container"></div>
    </div>

    <script type="module">
        import { initializeApp } from 'https://www.gstatic.com/firebasejs/10.13.2/firebase-app.js';
        import {
            getAuth,
            RecaptchaVerifier,
            signInWithPhoneNumber,
        } from 'https://www.gstatic.com/firebasejs/10.13.2/firebase-auth.js';

        const firebaseConfig = {
            apiKey: @json(config('services.firebase.api_key')),
            authDomain: @json(config('services.firebase.auth_domain')),
            projectId: @json(config('services.firebase.project_id')),
            appId: @json(config('services.firebase.app_id')),
        };

        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);

        const phoneForm = document.getElementById('phone-form');
        const otpForm = document.getElementById('otp-form');
        const errorBox = document.getElementById('auth-error');
        const sendBtn = document.getElementById('send-otp-btn');
        const verifyBtn = document.getElementById('verify-otp-btn');

        let confirmationResult = null;

        function showError(message) {
            errorBox.textContent = message;
            errorBox.classList.remove('hidden');
        }

        function clearError() {
            errorBox.classList.add('hidden');
        }

        function setLoading(button, loading, label) {
            button.disabled = loading;
            button.textContent = loading ? 'थांबा, जरा वेट करा…' : label;
        }

        window.recaptchaVerifier = new RecaptchaVerifier(auth, 'recaptcha-container', {
            size: 'invisible',
        });

        phoneForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            clearError();

            const digits = document.getElementById('mobile').value.replace(/\D/g, '');

            if (digits.length !== 10) {
                showError('बरोबर 10 अंकी मोबाईल नंबर टाका.');
                return;
            }

            const phoneNumber = `+91${digits}`;

            setLoading(sendBtn, true, 'OTP पाठवा');

            try {
                confirmationResult = await signInWithPhoneNumber(auth, phoneNumber, window.recaptchaVerifier);
                phoneForm.classList.add('hidden');
                otpForm.classList.remove('hidden');
            } catch (error) {
                console.error(error);
                showError('OTP सेंड झाला नाही. नंबर तपासून परत ट्राय करा.');
                window.recaptchaVerifier.render().then((widgetId) => {
                    if (window.grecaptcha) window.grecaptcha.reset(widgetId);
                });
            } finally {
                setLoading(sendBtn, false, 'OTP पाठवा');
            }
        });

        otpForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            clearError();

            const otp = document.getElementById('otp').value.trim();

            if (!confirmationResult) {
                showError('नवीन OTP मागा, कृपया.');
                return;
            }

            setLoading(verifyBtn, true, 'OTP व्हेरिफाय करा');

            try {
                const userCredential = await confirmationResult.confirm(otp);
                const idToken = await userCredential.user.getIdToken();

                const response = await fetch(@json(route('auth.verify-token')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        Accept: 'application/json',
                    },
                    body: JSON.stringify({ id_token: idToken }),
                });

                const data = await response.json();

                if (!response.ok) {
                    showError(data.message ?? 'व्हेरिफिकेशन फेल झालं. परत ट्राय करा.');
                    return;
                }

                window.location.href = data.redirect;
            } catch (error) {
                console.error(error);
                showError('OTP चुकीचा आहे किंवा एक्सपायर झाला. परत ट्राय करा.');
            } finally {
                setLoading(verifyBtn, false, 'OTP व्हेरिफाय करा');
            }
        });

        document.getElementById('change-number-btn').addEventListener('click', () => {
            otpForm.classList.add('hidden');
            phoneForm.classList.remove('hidden');
            document.getElementById('otp').value = '';
            confirmationResult = null;
        });
    </script>
</x-layouts.app>
