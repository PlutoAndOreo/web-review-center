<!-- Sidebar -->
<aside id="student-sidebar" 
class="fixed inset-y-0 left-0 z-40 w-64 transform -translate-x-full bg-black-100 border-r
           transition-transform duration-300 ease-in-out
           md:translate-x-0 md:static md:inset-0">
    <div class="flex h-full flex-col">

        <!-- Logo / Title -->
        <div class="px-6 py-4 text-lg font-bold border-b flex gap-1 items-center sidebar-text">
            Student Panel
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4  space-y-2 gap-5 flex flex-col">
            <div class="px-6 text-lg font-bold flex gap-1 items-center hover:bg-gray-200 rounded">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                    <path fill-rule="evenodd"
                        d="M9.293 2.293a1 1 0 0 1 1.414 0l7 7A1 1 0 0 1 17 11h-1v6a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-3a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-6H3a1 1 0 0 1-.707-1.707l7-7Z"
                        clip-rule="evenodd" />
                </svg>
                <a href="{{route('student.dashboard')}}" class="sidebar-text block rounded-lg px-4 py-2 text-sm font-medium">
                    Home
                </a>
            </div>
            <div class="px-6 text-lg font-bold flex gap-1 items-center hover:bg-gray-200 rounded">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                    <path fill-rule="evenodd"
                        d="M18.685 19.097A9.723 9.723 0 0 0 21.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12a9.723 9.723 0 0 0 3.065 7.097A9.716 9.716 0 0 0 12 21.75a9.716 9.716 0 0 0 6.685-2.653Zm-12.54-1.285A7.486 7.486 0 0 1 12 15a7.486 7.486 0 0 1 5.855 2.812A8.224 8.224 0 0 1 12 20.25a8.224 8.224 0 0 1-5.855-2.438ZM15.75 9a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"
                        clip-rule="evenodd" />
                </svg>
                <a href="{{route('student.info')}}" class="sidebar-text block rounded-lg px-4 py-2 text-sm font-medium">
                    Profile
                </a>
            </div>
            <div class="px-6  text-lg font-bold flex gap-1 items-center hover:bg-gray-200 rounded">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                    <path
                        d="M4.5 4.5a3 3 0 0 0-3 3v9a3 3 0 0 0 3 3h8.25a3 3 0 0 0 3-3v-9a3 3 0 0 0-3-3H4.5ZM19.94 18.75l-2.69-2.69V7.94l2.69-2.69c.944-.945 2.56-.276 2.56 1.06v11.38c0 1.336-1.616 2.005-2.56 1.06Z" />
                </svg>
                <a href="{{ route('student.videos.list') }}" class="sidebar-text block rounded-lg px-4 py-2 text-sm font-medium">

                    Videos
                </a>
            </div>

        </nav>

        <!-- Logout (bottom) -->
        <div class="border-t px-4 py-4">
            <form method="POST" action="{{ route('student.logout') }}">
                @csrf
                <div class="px-6 py-4 text-lg font-bold flex gap-1 items-center hover:bg-gray-300 rounded">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                        <path fill-rule="evenodd"
                            d="M16.5 3.75a1.5 1.5 0 0 1 1.5 1.5v13.5a1.5 1.5 0 0 1-1.5 1.5h-6a1.5 1.5 0 0 1-1.5-1.5V15a.75.75 0 0 0-1.5 0v3.75a3 3 0 0 0 3 3h6a3 3 0 0 0 3-3V5.25a3 3 0 0 0-3-3h-6a3 3 0 0 0-3 3V9A.75.75 0 1 0 9 9V5.25a1.5 1.5 0 0 1 1.5-1.5h6ZM5.78 8.47a.75.75 0 0 0-1.06 0l-3 3a.75.75 0 0 0 0 1.06l3 3a.75.75 0 0 0 1.06-1.06l-1.72-1.72H15a.75.75 0 0 0 0-1.5H4.06l1.72-1.72a.75.75 0 0 0 0-1.06Z"
                            clip-rule="evenodd" />
                    </svg>
                    <button class="sidebar-text block rounded-lg px-4 py-2 text-sm font-medium ">
                        Logout
                    </button>
                </div>

            </form>
        </div>

    </div>
</aside>
<script>

</script>