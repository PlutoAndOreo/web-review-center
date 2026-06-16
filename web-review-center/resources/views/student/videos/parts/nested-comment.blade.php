<div class="bg-gray-50 p-4 rounded-lg border mt-2">

    <div class="flex justify-between">
        <p class="font-semibold text-sm text-gray-900">
            {{ $comment->student?->first_name ?? $comment->admin?->first_name ?? 'User' }}
        </p>

        <span class="text-xs text-gray-400">
            {{ $comment->created_at->diffForHumans() }}
        </span>
    </div>

    <p class="text-gray-700 mt-2">
        {{ $comment->content }}
    </p>

    {{-- Reply Button --}}
    <button
        type="button"
        class="text-xs text-blue-600 mt-2"
        onclick="showReplyForm({{ $comment->id }})"
    >
        Reply
    </button>

    {{-- Reply Form --}}
    <form
        id="reply-form-{{ $comment->id }}"
        class="hidden mt-3"
        method="POST"
        action="{{ route('student.comments.store', $comment->video_id) }}"
    >
        @csrf

        <input
            type="hidden"
            name="parent_id"
            value="{{ $comment->id }}"
        >

        <textarea
            name="message"
            rows="3"
            class="w-full border rounded p-2"
            placeholder="Write a reply..."
        ></textarea>

        <button
            type="submit"
            class="mt-2 px-3 py-1 bg-blue-600 text-white rounded"
        >
            Submit Reply
        </button>
    </form>

    {{-- Nested Replies --}}
    @if($comment->replies->isNotEmpty())
        <div class="ml-6 mt-4 space-y-3 border-l-2 border-blue-200 pl-4">

            @foreach($comment->replies as $reply)

                {{-- Recursive rendering --}}
                @include('student.videos.parts.nested-comment', [
                    'comment' => $reply
                ])

            @endforeach

        </div>
    @endif

</div>
