@extends('student.layouts.app')

@section('title', 'Videos')

@section('content')

  <div
  {{-- class="max-w-6xl mx-auto bg-white p-6 rounded-xl shadow" --}}

      class="max-w-5xl mx-auto bg-white p-6 rounded-xl shadow"
      x-data="{ progress: 0, completed: false, showExam: false }">
      <!--  HEADER -->
      <div class="flex items-center justify-between mb-4">

          <!-- Back Button -->
          <a href="{{ route('student.videos.list', ['sort' => 'asc']) }}"
            class="text-green-600 font-medium hover:underline">
              ← Back
          </a>

          <!-- Subject -->
          <span class="text-sm text-gray-500">
              {{ $video->subject->name ?? 'Subject' }}
          </span>

      </div>

      <!--  TITLE -->
      <h1 class="text-2xl font-bold text-gray-900 mb-6">
          {{ $video->title }}
      </h1>

      <!--  VIDEO -->
      <div
          x-data="videoPlayer('{{ route('stream.video', $video->id) }}')"
          x-init="init()"
          class="bg-black rounded-lg overflow-hidden mb-6">

          <video
              x-ref="video"
              {{-- class="w-full aspect-video object-contain" --}}

              class="w-full h-[400px]"
              @timeupdate="
                  let v = $event.target;

                  if (v.duration) {
                      progress = Math.floor((v.currentTime / v.duration) * 100);
                  }

                  if (progress >= 90) {
                      completed = true;
                  }
              "
              {{ $video->url }}
          </video>

    <button
        @click="toggle()"
        class="absolute inset-0 flex items-center justify-center"
    >

        <div class="bg-white/80 rounded-full p-4 shadow-lg transition transform hover:scale-110">

            <!-- PLAY ICON -->
            <svg x-show="!playing"
                 xmlns="http://www.w3.org/2000/svg"
                 class="w-8 h-8 text-black"
                 fill="currentColor"
                 viewBox="0 0 24 24">
                <path d="M5 3v18l15-9L5 3Z"/>
            </svg>

            <!-- PAUSE ICON -->
            <svg x-show="playing"
                 xmlns="http://www.w3.org/2000/svg"
                 class="w-8 h-8 text-black"
                 fill="currentColor"
                 viewBox="0 0 24 24">
                <path d="M6 4h4v16H6zM14 4h4v16h-4z"/>
            </svg>

        </div>

    </button>


    </div>

      <button
          @click="showExam = !showExam"
          class="w-full mb-6 py-3 rounded-lg font-semibold transition
                bg-green-600 text-white hover:bg-green-700
                disabled:bg-gray-300 disabled:cursor-not-allowed">
          <span x-text="showExam ? 'Hide Exam' : 'Take Exam'"></span>
      </button>
      <!--  GOOGLE FORM (ALPINE CONTROLLED) -->
      <div x-show="showExam" x-transition class="border-t pt-6">

          <h2 class="text-lg font-semibold mb-4 text-gray-900">
              Exam
          </h2>

          <div class="w-full overflow-hidden rounded-lg border">

              <iframe
                  src="{{ $video->google_form_link }}"
                  class="w-full h-[700px]"
                  frameborder="0"
              >
                  Loading…
              </iframe>

          </div>
          <button
              type="submit"
              class="w-full bg-green-600 text-white py-3 rounded-lg
                    font-semibold hover:bg-green-700 transition
                    focus:outline-none focus:ring-2 focus:ring-green-300">
              Submit
          </button>
      </div>

    <div class="mt-10 border-t pt-6">

    <!--  Header -->
    <h2 class="text-lg font-semibold text-gray-900 mb-4">
        Comments
    </h2>

    <!--  Comment Form -->
    <div x-data="{ comment: '', loading: false }" class="mb-6">

        <textarea
            x-model="comment"
            rows="3"
            placeholder="Write your comment..."
            class="w-full px-4 py-2 border rounded-lg
                   focus:outline-none focus:ring-2 focus:ring-green-200 focus:border-green-400"
        ></textarea>

        <!-- Actions -->
        <div class="flex justify-between items-center mt-2">

            <span class="text-sm text-gray-400">
                Share your thoughts about this lesson
            </span>

            <button
                @click="
                    if(comment.trim() !== '') {
                        loading = true;

                        // simulate submit
                        setTimeout(() => {
                            comments.push({
                                text: comment,
                                user: '{{ auth()->user()->first_name ?? 'You' }}'
                            });
                            comment = '';
                            loading = false;
                        }, 500);
                    }
                "
                :disabled="loading"
                class="bg-green-600 text-white px-4 py-2 rounded-lg
                       hover:bg-green-700 transition
                       disabled:bg-gray-300 disabled:cursor-not-allowed"
            >
                <span x-show="!loading">Post</span>
                <span x-show="loading">Posting...</span>
            </button>

        </div>
    </div>

    <!--  Comment List -->
    <div x-data="{ comments: [] }" class="space-y-4">

        <!-- Empty State -->
        <p x-show="comments.length === 0" class="text-sm text-gray-500">
            No comments yet. Be the first to comment.
        </p>

        <!-- Loop Comments -->
        <template x-for="(item, index) in comments" :key="index">
            <div class="bg-gray-50 p-4 rounded-lg border">

                <div class="flex items-center justify-between">

                    <p class="font-semibold text-sm text-gray-900"
                       x-text="item.user">
                    </p>

                    <span class="text-xs text-gray-400">
                        Just now
                    </span>

                </div>

                <p class="text-gray-700 mt-2" x-text="item.text"></p>

            </div>
        </template>

    </div>

  </div>
@endsection
@push('js')
<script>

function videoPlayer(url) {
    return {
        playing: false,
        progress: 0,
        completed: false,

        init() {
            const video = this.$refs.video;

            // ✅ HLS support
            if (Hls.isSupported()) {
                const hls = new Hls();
                hls.loadSource(url);
                hls.attachMedia(video);
            } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                video.src = url;
            }

            // ✅ Sync state
            video.addEventListener('play', () => this.playing = true);
            video.addEventListener('pause', () => this.playing = false);
        },

        toggle() {
            const video = this.$refs.video;
            video.paused ? video.play() : video.pause();
        }
    }
}

</script>

