<div class="mt-4">
        <input id="videoId" type="hidden" value="{{$videoId}}" />
        
        <div x-show="showExam" x-transition class="border-t pt-6">
            <div class="w-full overflow-hidden rounded-lg border">
                <iframe
                    src="{{ $video->google_form_link }}"
                    class="w-full h-[700px]"
                    frameborder="0"
                ></iframe>
            </div>

            <button
                type="button"
                onclick="confirmSubmission()"
                class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 transition disabled:bg-gray-300"
            >
                Confirm Submission
            </button>
        </div>
    </div>
