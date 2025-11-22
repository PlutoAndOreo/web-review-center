<!-- Idle Timeout Warning Modal -->
<div id="sessionWarning"
     class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white p-6 rounded-xl shadow-xl w-80 text-center">
        <h2 class="text-lg font-semibold text-gray-800">Are you still there?</h2>
        <p class="mt-2 text-gray-600">
            You will be logged out in 
            <span id="countdown" class="font-bold text-red-600">120</span> seconds.
        </p>
        <button id="stayLoggedInBtn"
            class="mt-4 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg w-full">
            Stay Logged In
        </button>
    </div>
</div>

<script>
    const MAX_IDLE_MINUTES = 15; 
    const WARNING_MINUTES  = 13; 

    let idleMinutes = 0;
    let warningShown = false;
    let warningCountdown = 120;
    let countdownTimer = null;

    function resetIdle() {
        idleMinutes = 0;
        if (warningShown) hideWarning();
    }

    function showWarning() {
        warningShown = true;
        warningCountdown = 120;

        document.getElementById("sessionWarning").classList.remove("hidden");

        countdownTimer = setInterval(() => {
            warningCountdown--;
            document.getElementById("countdown").textContent = warningCountdown;

            if (warningCountdown <= 0) {
                logoutUser();
            }
        }, 1000);
    }

    function hideWarning() {
        warningShown = false;
        clearInterval(countdownTimer);
        document.getElementById("sessionWarning").classList.add("hidden");
    }

    async function logoutUser() {
        await fetch("{{ route('auto.logout') }}", {
            method: "POST",
            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
        });

        window.location.href = "{{ route('admin.login') }}";
    }

    document.getElementById("stayLoggedInBtn").onclick = function () {
        hideWarning();
        resetIdle();
    };

    window.onload = resetIdle;
    document.onmousemove = resetIdle;
    document.onkeypress  = resetIdle;
    document.onclick     = resetIdle;
    document.onscroll    = resetIdle;

    setInterval(() => {
        idleMinutes++;

        if (!warningShown && idleMinutes === WARNING_MINUTES) {
            showWarning();
        }

        if (idleMinutes >= MAX_IDLE_MINUTES) {
            logoutUser();
        }

    }, 60000);
</script>
