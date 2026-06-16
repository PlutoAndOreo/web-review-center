
<div class="mt-10 border-t pt-6">

    <h2 class="text-lg font-semibold text-gray-900 mb-4">
        Comments
    </h2>

    <form action ="{{ route('student.comments.store', $video->id) }}" method="POST">
        @csrf

        <textarea
            name="message"
            rows="3"
            placeholder="Write your comment..."
            class="w-full px-4 py-2 border rounded-lg focus:outline-none"
        ></textarea>

        <input type="hidden" name="video_id" value="{{ $video->id }}">

        <div class="flex justify-between items-center mt-2">

            <span class="text-sm text-gray-400">
                Share your thoughts about this lesson
            </span>

            <button
                type="submit"
                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition"
            >
                Post
            </button>

        </div>
    </form>
</div>
<div class="space-y-4 mt-6">

    @forelse ($comments as $comment)
        @include('student.videos.parts.nested-comment', ['comment' => $comment])
    @empty
        <div class="text-center py-8 text-gray-500">
            No comments yet.
        </div>
    @endforelse

</div>
