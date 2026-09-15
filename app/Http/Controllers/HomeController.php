<?php

namespace App\Http\Controllers;

use App\Models\Festival;
use App\Models\GalleryPhoto;
use App\Models\Game;
use App\Models\MahaprasadSlot;

class HomeController extends Controller
{
    public function index()
    {
        $festival = Festival::active();

        $games = Game::query()->orderBy('date')->orderBy('time')->get();

        $mahaprasadSlots = MahaprasadSlot::query()
            ->where('status', 'approved')
            ->with('submitter')
            ->orderBy('date')
            ->get();

        $photos = GalleryPhoto::query()->orderBy('sort_order')->get();
        $heroPhoto = $photos->first();

        $winners = Game::query()
            ->whereHas('participants')
            ->with(['participants' => fn ($q) => $q->with('member')->orderByRaw("FIELD(position, 'first', 'second', 'third', 'participation')")])
            ->orderByDesc('date')
            ->get();

        return view('welcome', [
            'festival' => $festival,
            'games' => $games,
            'mahaprasadSlots' => $mahaprasadSlots,
            'photos' => $photos,
            'heroPhoto' => $heroPhoto,
            'winners' => $winners,
        ]);
    }
}
