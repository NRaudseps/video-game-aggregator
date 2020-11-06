<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;

class PopularGames extends Component
{
    public $ratedGames = [];

    public function loadPopularGames()
    {
        $before = Carbon::now()->subMonths(2)->timestamp;
        $after = Carbon::now()->addMonths(2)->timestamp;

        $ratedGamesUnformatted = Cache::remember('rated-games', 7, function () use ($before, $after) {
            return Http::withHeaders(config('services.igdb.headers'))
                ->withBody(
            "fields name, cover.url, first_release_date, total_rating_count, platforms.abbreviation, rating, slug;
                    where platforms = (48, 49, 130,6)
                    & ( first_release_date >= {$before}
                    & first_release_date < {$after}
                    & total_rating_count > 5);
                    sort total_rating_count desc;
                    limit 12;"
                    , 'text/plain')
                ->post(config('services.igdb.endpoint'))
                ->json();
        });

        $this->ratedGames = $this->formatForView($ratedGamesUnformatted);

        collect($this->ratedGames)->filter(function ($game){
            return $game['rating'];
        })->each(function ($game) {
            $this->emit('gameWithRatingAdded', [
                'slug' => $game['slug'],
                'rating' => $game['rating'] / 100
            ]);
        });

//        dd($this->ratedGames);
    }

    public function render()
    {
        return view('livewire.popular-games');
    }

    protected function formatForView($games)
    {
        return collect($games)->map(function ($game) {
            return collect($game)->merge([
               'coverImageUrl' => Str::replaceFirst('thumb', 'cover_big', $game['cover']['url']),
                'rating' => isset($game['rating']) ? round($game['rating']) : null,
                'platforms' => collect($game['platforms'])->pluck('abbreviation')->filter()->implode(', ')
            ]);
        })->toArray();
    }
}
