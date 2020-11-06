@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4">
        <div class="game-details border-b border-gray-800 pb-12 flex flex-col lg:flex-row">
            <div class="flex-none">
                <img src="{{ $game['coverImageUrl'] }}" alt="cover">
            </div>
            <div class="lg:ml-12 lg:mr-64">
                <h2 class="font-semibold text-4xl leading-tight mt-1">{{ $game['name'] }}</h2>
                <div class="test-gray-400">
                    <span>
                        {{ $game['genres'] }}
                    </span>
                    &middot;
                    <span>{{ $game['involved_companies'] }}</span>
                    &middot;
                    <span>{{ $game['platforms'] }}</span>
                </div>
                <div class="flex flex-wrap items-center mt-8">
                    <div class="flex items-center">
                        <div id="memberRating" class="w-16 h-16 bg-gray-800 rounded-full relative text-sm">
                            @push('scripts')
                                @include('_rating', [
                                    'slug' => 'memberRating',
                                    'rating' => $game['memberRating'],
                                    'event' => null,
                                ])
                            @endpush
                        </div>
                        <div class="ml-4 text-xs">Member <br> Score</div>
                    </div>
                    <div class="flex items-center ml-12">
                        <div id="criticRating" class="w-16 h-16 bg-gray-800 rounded-full relative text-sm">
                            @push('scripts')
                                @include('_rating', [
                                    'slug' => 'criticRating',
                                    'rating' => $game['criticRating'],
                                    'event' => null,
                                ])
                            @endpush
                        </div>
                        <div class="ml-4 text-xs">Critic <br> Score</div>
                    </div>
                    <div class="flex items-center space-x-4 mt-4 lg:mt-0 lg:ml-12">
                        @if($game['social']['website'])
                            <div class="w-8 h-8 bg-gray-800 rounded-full flex justify-center items-center">
                                <a href="{{ $game['social']['website']['url'] }}" class="hover:text-gray-400">w</a>
                            </div>
                        @endif
                        @if($game['social']['facebook'])
                            <div class="w-8 h-8 bg-gray-800 rounded-full flex justify-center items-center">
                                <a href="{{ $game['social']['facebook']['url'] }}" class="hover:text-gray-400">i</a>
                            </div>
                        @endif
                        @if($game['social']['twitter'])
                            <div class="w-8 h-8 bg-gray-800 rounded-full flex justify-center items-center">
                                <a href="{{ $game['social']['twitter']['url'] }}" class="hover:text-gray-400">t</a>
                            </div>
                        @endif
                        @if($game['social']['instagram'])
                            <div class="w-8 h-8 bg-gray-800 rounded-full flex justify-center items-center">
                                <a href="{{ $game['social']['instagram']['url'] }}" class="hover:text-gray-400">f</a>
                            </div>
                        @endif
                    </div>
                </div>

                <p class="mt-12">{{ $game['summary'] }}</p>

                <div class="mt-12" x-data="{ isTrailerModalVisible: false}">
                    <button
                        @click="isTrailerModalVisible = true"
                        class="inline-flex bg-blue-500 text-white font-semibold px-4 py-4 hover:bg-blue-600
                        rounded transition ease-in-out duration-150">
                        <span class="ml-2">Play Trailer</span>
                    </button>

                    <template x-if="isTrailerModalVisible">
                        <div
                            style="background-color: rgba(0, 0, 0, .5)"
                            class="z-50 fixed top-0 left-0 w-full h-full flex items-center shadow-lg overflow-y-auto">
                            <div class="container mx-auto lg:px-32 rounded-lg overflow-y-auto">
                                <div class="bg-gray-900 rounded">
                                    <div class="flex justify-end pr-4 pt-2">
                                        <button
                                            @click="isTrailerModalVisible = false"
                                            @keydown.escape.window="isTrailerModalVisible = false"
                                            class="text-3xl leading-none hover:text-gray-300">&times</button>
                                    </div>
                                    <div class="modal-body px-8 py-8">
                                        <div class="responsive-container overflow-hidden relative" style="padding-top: 56.25%">
                                            <iframe width="500" height="315" class="responsive-iframe absolute top-0 left-0
                                            w-full h-full" src="{{ $game['trailer'] }}" style="border:0;" allow="autoplay; encrypted-media"
                                            allowfullscreen>
                                            </iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

            </div>
        </div> <!-- end game details-->
        <div class="images-container border-b border-gray-800 pb-12 mt-8"
             x-data="{ isImageModalVisible: false, image: '' }"
        >
            <h2 class="text-blue-500 uppercase tracking-wide font-semibold">Images</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12 mt -8">
                @foreach($game['screenshots'] as $screenshot)
                    <div>
                        <a href="#"
                            @click.prevent="
                                isImageModalVisible = true
                                image='{{ $screenshot['huge'] }}'
                            ">
                            <img src="{{ $screenshot['big'] }}" alt="screenshot"
                                 class="hover:opacity-75 transition ease-in-out duration-150">
                        </a>
                    </div>
                @endforeach
            </div>

            <template x-if="isImageModalVisible">
                <div
                    style="background-color: rgba(0, 0, 0, .5)"
                    class="z-50 fixed top-0 left-0 w-full h-full flex items-center shadow-lg overflow-y-auto">
                    <div class="container mx-auto lg:px-32 rounded-lg overflow-y-auto">
                        <div class="bg-gray-900 rounded">
                            <div class="flex justify-end pr-4 pt-2">
                                <button
                                    @click="isImageModalVisible = false"
                                    @keydown.escape.window="isImageModalVisible = false"
                                    class="text-3xl leading-none hover:text-gray-300">&times</button>
                            </div>
                            <div class="modal-body px-8 py-8">
                                <img :src="image" alt="screenshot">
                            </div>
                        </div>
                    </div>
            </template>
        </div> <!-- end images-container -->

        <div class="similar-games-container mt-8">
            <h2 class="text-blue-500 uppercase tracking-wide font-semibold">Similar Games</h2>
            <div
                class="similar-games text-sm grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 xl:grid-cols-6 gap-12">
                @foreach($game['similarGames'] as $game)
                    <div class="game mt-8">
                        <div class="relative inline-block">
                            <a href="#">
                                <img src="{{ $game['coverImageUrl']  }}"
                                     alt="game cover" class="hover:opacity-75 transition ease-in-out duration-150">
                            </a>
                            <div id="{{ $game['slug'] }}" class="absolute bottom-0 right-0 w-16 h-16 bg-gray-800 rounded-full"
                                 style="right:-20px; bottom:-20px">
                            </div>

                            @push('scripts')
                                @include('_rating', [
                                    'slug' => $game['slug'],
                                    'rating' => $game['rating'],
                                    'event' => null,
                                ])
                            @endpush

                        </div>
                        <a href="#" class="block text-base font-semibold leading-tight hover:text-gray-400 mt-8">
                            {{ $game['name'] }}
                        </a>
                        <div class="text-gray-400 mt-1">
                            {{ $game['platforms'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div> <!-- end similar-games -->
    </div>
@endsection
