@php
    $type = auth()->guard('student')->user()->type;
@endphp
<aside aria-label="Student sidebar"
    class="w-64 min-h-screen  bg-green-950 text-white flex flex-col items-center py-8 shadow-xl">
    <!-- Avatar -->
    <div class="mb-6">
        <div class="w-24 h-24 rounded-full bg-white flex items-center justify-center shadow-lg">
            <!-- User icon -->
            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                 viewBox="0 0 24 24" class="w-12 h-12 text-[#183A5A]">
                <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm0 2c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5Z"/>
            </svg>
        </div>
    </div>

    <!-- User Info -->
    <div class="text-center mb-8">
        <p class="text-lg font-semibold tracking-wide uppercase">
                {{ $student->first_name }} {{ $student->last_name }}
        </p>
        <p class="text-sm text-gray-300">
                {{ $student->email }}
        </p>
    </div>

    <!-- Navigation -->
    <nav class="w-full px-6">
        <ul class="space-y-10 text-sm text-gray-200">
            @if($student->type == \App\Models\Student::TYPE_ONLINE)
            <li>
                <a href="{{ route('student.dashboard') }}"
                   class="flex items-center gap-3 px-4 py-2 rounded-lg transition
                        {{ request()->routeIs('student.dashboard')
                            ? 'bg-white/20 text-white font-semibold'
                            : 'text-gray-300 hover:bg-white/10 hover:text-white' }}"
                >

                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>

                <span>Dashboard</span>
                </a>
            </li>
            @endif
            <li>
                <a href="{{ route('student.videos.list', ['sort' => 'asc']) }}"
                   class="flex items-center gap-3 px-4 py-2 rounded-lg transition
                        {{ request()->routeIs('student.videos.list')
                            ? 'bg-white/20 text-white font-semibold'
                            : 'text-gray-300 hover:bg-white/10 hover:text-white' }}"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>

                    <span>Videos</span>
                </a>
            </li>
            @if($student->type == \App\Models\Student::TYPE_ONLINE)

            <li>
                <a href="{{ route('student.info') }}"
                   class="flex items-center gap-3 px-4 py-2 rounded-lg transition
                        {{ request()->routeIs('student.info')
                            ? 'bg-white/20 text-white font-semibold'
                            : 'text-gray-300 hover:bg-white/10 hover:text-white' }}"
                >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                </svg>
                    <span>Profile</span>
                </a>
            </li>
            @endif
            <li>
                <form method="POST" action="{{ route('student.logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="w-full flex items-center gap-3 px-4 py-2 rounded-lg transition
                            text-gray-300 hover:bg-white/10 hover:text-white"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            </li>



        </ul>
    </nav>
</aside>
