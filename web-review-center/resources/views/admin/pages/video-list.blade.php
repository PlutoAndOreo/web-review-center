@extends('adminlte::page')

@section('title', 'Video List')

@section('css')
    @vite('resources/css/app.css')
@endsection

@section('content')
<div class="overflow-x-auto py-3">
    
    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex justify-left mb-3">
        <a href="{{ route('videos.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm font-semibold rounded-lg shadow hover:bg-blue-600 transition-colors">
            <i class="fas fa-plus mr-2"></i> Add Video
        </a>
    </div>   

    <table class="w-full border border-gray-200 rounded-lg">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600 border-b">Title</th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600 border-b">Video thumbnail</th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600 border-b">Google form</th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600 border-b">Date</th>
                <th class="px-4 py-2 text-center text-sm font-semibold text-gray-600 border-b">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach ($videos as $video)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 text-sm text-gray-700">{{ $video->title }}</td>
                    <td class="px-4 py-2 text-sm text-gray-700">
                        @if ($video->video_thumb)
                            <img src="{{ url(ltrim($video->video_thumb, '/')) }}" 
                                alt="Video Thumbnail" 
                                class="w-20 h-12 object-cover rounded">
                        @else
                            <span class="text-red-500 italic">The video is currently unavailable for viewing.</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-sm text-gray-700 max-w-[200px] truncate">
                        <a href="{{ $video->google_form_upload }}" 
                        target="_blank" 
                        class="text-blue-600 hover:underline">
                            {{ $video->google_form_upload }}
                        </a>
                    </td>

                    <td class="px-4 py-2 text-sm text-gray-700">
                        {{ \Carbon\Carbon::parse($video->created_at)->format('Y-m-d') }}
                    </td>
                    <td class="px-4 py-2 text-center flex justify-center gap-3">
                        <a href="{{ route('videos.edit', ['id' => $video->id]) }}" class="text-blue-600 hover:text-blue-800" title="Edit">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <form id="delete-form-{{ $video->id }}" action="{{ route('videos.destroy', ['id' => $video->id]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="text-red-600 hover:text-red-800 btn-open-delete" data-id="{{ $video->id }}" title="Delete">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>    
<!-- Delete Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-lg shadow-lg">
        <div class="px-5 py-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Delete Video</h3>
        </div>
        <div class="px-5 py-4">
            <p class="text-sm text-gray-700">Are you sure you want to delete this video? This action cannot be undone.</p>
        </div>
        <div class="px-5 py-4 border-t flex justify-end gap-2">
            <button type="button" id="btn-cancel-delete" class="px-4 py-2 rounded border text-gray-700 hover:bg-gray-50">Cancel</button>
            <button type="button" id="btn-confirm-delete" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">Delete</button>
        </div>
    </div>
    <form id="delete-target-form" method="POST" class="hidden"></form>
</div>

@section('js')
<script>
    (function(){
        const modal = document.getElementById('deleteModal');
        const cancelBtn = document.getElementById('btn-cancel-delete');
        const confirmBtn = document.getElementById('btn-confirm-delete');
        const targetForm = document.getElementById('delete-target-form');
        let currentForm = null;

        function openModal(form){
            currentForm = form;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal(){
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            currentForm = null;
        }

        document.querySelectorAll('.btn-open-delete').forEach(function(btn){
            btn.addEventListener('click', function(){
                const id = this.getAttribute('data-id');
                const form = document.getElementById('delete-form-' + id);
                openModal(form);
            });
        });

        cancelBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', function(e){
            if (e.target === modal) closeModal();
        });

        confirmBtn.addEventListener('click', function(){
            if (!currentForm) return;
            currentForm.submit();
            closeModal();
        });
    })();
</script>
@endsection
@stop