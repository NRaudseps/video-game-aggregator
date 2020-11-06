<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GamesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param string $slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $game = Http::withHeaders(config('services.igdb.headers'))
            ->withBody(
                "fields name, cover.url, genres.name, involved_companies.company.name, platforms.abbreviation,
                rating, aggregated_rating, summary, videos.*, screenshots.*, similar_games.name, similar_games.url,
                similar_games.cover.url, similar_games.rating, similar_games.slug, websites.*;
                    where slug=\"{$slug}\";"
                , 'text/plain')
            ->post(config('services.igdb.endpoint'))
            ->json();

        abort_if(!$game, 404);

//        dd($this->formatGameForView($game[0]));

        return view('show', [
            'game' => $this->formatGameForView($game[0])
        ]);
    }

    protected function formatGameForView($game)
    {
        return collect($game)->merge([
            'coverImageUrl' => Str::replaceFirst('thumb', 'cover_big', $game['cover']['url']),
            'genres' => isset($game['genres']) ?
                collect($game['genres'])->pluck('name')->implode(', ') : null,
            'involved_companies' => $game['involved_companies'][0]['company']['name'],
            'platforms' => collect($game['platforms'])->pluck('abbreviation')->implode(', '),
            'memberRating' => array_key_exists('rating', $game) ? round($game['rating']) : '0',
            'criticRating' => array_key_exists('aggregated_rating', $game) ? round($game['aggregated_rating']) : '0',
            'trailer' => 'https://youtube.com/embed/' . $game['videos'][0]['video_id'],
            'screenshots' => collect($game['screenshots'])->map( function ($screenshot){
                return [
                    'big' => Str::replaceFirst('thumb', 'screenshot_big', $screenshot['url']),
                    'huge' => Str::replaceFirst('thumb', 'screenshot_huge', $screenshot['url'])
                ];
            })->take(9),
            'similarGames' => collect($game['similar_games'])->map(function ($game){
                return collect($game)->merge([
                   'coverImageUrl' => array_key_exists('cover', $game)
                        ? Str::replaceFirst('thumb', 'cover_big', $game['cover']['url'])
                       : 'https://via.placholder.com/264x352',
                   'rating' => isset($game['rating']) ? round($game['rating']) : null,
                   'platforms' => array_key_exists('platforms', $game)
                        ? collect($game['platforms'])->pluck('abbreviation')->implode(', ')
                        : null,
                ]);
            })->take(6),
            'social' => [
                'website' => collect($game['websites'])->first(),
                'facebook' => collect($game['websites'])->filter(function ($websites){
                    return Str::contains($websites['url'], 'facebook');
                })->first(),
                'twitter' => collect($game['websites'])->filter(function ($websites){
                    return Str::contains($websites['url'], 'twitter');
                })->first(),
                'instagram' => collect($game['websites'])->filter(function ($websites){
                    return Str::contains($websites['url'], 'instagram');
                })->first(),
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
