<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameParticipant;
use App\Services\CertificateGenerator;
use Illuminate\Support\Facades\Gate;

class CertificateController extends Controller
{
    public function winner(GameParticipant $participant, CertificateGenerator $generator)
    {
        Gate::authorize('update', $participant->game);

        return $this->pdf(
            $generator->generate(collect([$participant])),
            'certificate-'.$participant->game_id.'-'.$participant->id.'.pdf',
        );
    }

    public function game(Game $game, CertificateGenerator $generator)
    {
        Gate::authorize('update', $game);

        $winners = $game->participants()
            ->with('member')
            ->orderByRaw("FIELD(age_group, 'small', 'medium', 'large')")
            ->orderByRaw("FIELD(gender, 'male', 'female')")
            ->orderByRaw("FIELD(position, 'first', 'second', 'third')")
            ->get();

        abort_if($winners->isEmpty(), 404, 'No winners have been entered for this event yet.');

        return $this->pdf($generator->generate($winners), 'certificates-'.$game->date->format('Y-m-d').'-'.$game->id.'.pdf');
    }

    private function pdf(string $content, string $filename)
    {
        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
    }
}
