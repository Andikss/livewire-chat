<div class="max-w-6xl mx-auto my-16">
    <h5 class="text-center text-5xl font-bold py-3">
        Users
    </h5>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 p-2">

        @foreach ($users as $index => $user)
            <div class="w-full bg-white border border-gray-200 rounded-lg shadow md:col-span-1 lg:col-span-1 xl:col-span-1">
                <div class="flex flex-col items-center py-8 p-3">
                    <img src="https://source.unsplash.com/500x500?face-{{ $index }}" alt="user" class="h-32 w-32 mb-2.5 rounded-full shadow-lg">

                    <h5 class="mb-1 text-xl font-medium text-gray-900">
                        {{ $user->name }}
                    </h5>

                    <span class="text-sm text-gray-500">
                        {{ $user->email }}
                    </span>

                    <div class="flex mt-4 space-x-3 md:mt-6">
                        <x-secondary-button>
                            Add Friend
                        </x-secondary-button>

                        <x-primary-button wire:click="message('{{ $user->id }}')">
                            Message
                        </x-primary-button>
                    </div>
                </div>
            </div>
        @endforeach

    </div>

</div>
