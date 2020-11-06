<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Component;

class RecentlyReviewed extends Component
{
    public $recentlyReviewed = [];

    public function loadRecentlyReviewed()
    {
        $before = Carbon::now()->subMonths(2)->timestamp;
        $current = Carbon::now()->timestamp;

        $recentlyReviewedUnformatted = Http::withHeaders(config('services.igdb'))
            ->withBody(
                "fields name, cover.url, first_release_date, rating, rating_count, platforms.abbreviation, summary, slug;
                where platforms = (48, 49, 130,6)
                & ( first_release_date >= {$before}
                & first_release_date < {$current}
                & rating_count > 5);
                where rating > 80;
                limit 3;"
                , 'text/plain')
            ->post('https://api.igdb.com/v4/games')->json();

        $this->recentlyReviewed = $this->formatForView($recentlyReviewedUnformatted);

        collect($this->recentlyReviewed)->filter(function ($game){
            return $game['rating'];
        })->each(function ($game) {
            $this->emit('reviewGameWithRatingAdded', [
                'slug' => 'review_'.$game['slug'],
                'rating' => $game['rating'] / 100
            ]);
        });
    }

    public function render()
    {
        return view('livewire.recently-reviewed');
    }

    protected function formatForView($games)
    {
        return collect($games)->map(function ($game) {
           return collect($game)->merge([
               'coverImageUrl' => Str::replaceFirst('thumb', 'cover_big', $game['cover']['url']),
               'rating' => isset($game['rating']) ? round($game['rating']) : null,
               'platforms' => collect($game['platforms'])->pluck('abbreviation')->implode(', ')
           ]);
        });
    }
}
