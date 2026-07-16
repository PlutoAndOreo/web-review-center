<div class="bg-gray-50 p-4 rounded-lg border mt-2">

    <div class="flex justify-between items-start">
        <p class="font-semibold text-sm text-gray-900">
            @if($comment->admin_id)
                {{ $comment->admin?->first_name }} {{ $comment->admin?->last_name }}
                <span class="text-xs text-blue-600">(Admin)</span>
            @elseif($comment->student_id)
                {{ $comment->student?->first_name }} {{ $comment->student?->last_name }}
            @else
                Deleted User
            @endif
        </p>

        <div class="flex items-center gap-2">
            @if(is_null($comment->admin_id) && auth('student')->id() === $comment->student_id)
            <form
                    action="{{ route('student.comments.destroy', [$comment->video_id ,$comment->id]) }}"
                    method="POST"
                    onsubmit="return confirm('Are you sure you want to delete this comment?')"
                >
                    @csrf
                    @method('DELETE')


                    <button
                        type="submit"
                        class="text-xs text-red-600 hover:text-red-800"
                    >
                        Delete
                    </button>


                </form>
            @endif
        </div>
    </div>

    <p class="text-gray-700 mt-2">
        {{ $comment->content }}
    </p>
    <span class="text-xs text-gray-400">
        {{ $comment->created_at->diffForHumans() }}
    </span>
    <br>

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
