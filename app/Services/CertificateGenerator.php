<?php

namespace App\Services;

use App\Models\Festival;
use App\Models\GameParticipant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class CertificateGenerator
{
    /**
     * Render one landscape A4 certificate page per winner into a single PDF.
     *
     * @param  Collection<int, GameParticipant>  $winners
     */
    public function generate(Collection $winners): string
    {
        $winners->each(fn (GameParticipant $winner) => $winner->loadMissing(['game', 'member']));

        $tempDir = storage_path('app/mpdf');
        File::ensureDirectoryExists($tempDir);

        $fontDirs = (new ConfigVariables)->getDefaults()['fontDir'];
        $fontData = (new FontVariables)->getDefaults()['fontdata'];

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'tempDir' => $tempDir,
            'fontDir' => [...$fontDirs, resource_path('fonts')],
            'fontdata' => $fontData + [
                'hind' => [
                    'R' => 'Hind-Regular.ttf',
                    'B' => 'Hind-Bold.ttf',
                    'useOTL' => 0xFF,
                ],
                'hindsemibold' => [
                    'R' => 'Hind-SemiBold.ttf',
                    'useOTL' => 0xFF,
                ],
                'yatraone' => [
                    'R' => 'YatraOne-Regular.ttf',
                    'useOTL' => 0xFF,
                ],
            ],
            'default_font' => 'hind',
        ]);

        $mpdf->SetTitle('Certificates');
        $mpdf->SetAuthor('Shakuntal Nagar Ganeshotsav Tarun Mandal');
        $mpdf->WriteHTML(view('certificates.styles')->render(), HTMLParserMode::HEADER_CSS);

        $bunting = '<img src="'.$this->buntingDataUri().'" style="width: 271mm; height: 11mm;">';

        foreach ($winners as $winner) {
            $data = [
                'winner' => $winner,
                'year' => $winner->game->date?->year ?? Festival::active()?->year ?? $winner->game->created_at->year,
                'date' => $winner->game->date?->locale('mr')->translatedFormat('j F Y'),
            ];

            $mpdf->AddPage();

            // Ivory paper, maroon outer frame, gold inner rule.
            $mpdf->SetFillColor(251, 243, 228);
            $mpdf->Rect(0, 0, 297, 210, 'F');
            $mpdf->SetDrawColor(43, 11, 14);
            $mpdf->SetLineWidth(2);
            $mpdf->Rect(7, 7, 283, 196, 'D');
            $mpdf->SetDrawColor(184, 137, 46);
            $mpdf->SetLineWidth(0.5);
            $mpdf->Rect(10, 10, 277, 190, 'D');

            $mpdf->WriteFixedPosHTML($bunting, 13, 12, 271, 11, 'hidden');
            $mpdf->WriteFixedPosHTML(view('certificates.content', $data)->render(), 10, 26, 277, 128, 'hidden');

            // Rank medal.
            $mpdf->SetFillColor(227, 192, 119);
            $mpdf->SetDrawColor(184, 137, 46);
            $mpdf->SetLineWidth(1.2);
            $mpdf->Circle(148.5, 172, 12.5, 'FD');
            $mpdf->WriteFixedPosHTML('<div class="medal-num">'.$winner->position->number().'</div>', 136, 167, 25, 12, 'hidden');

            $mpdf->WriteFixedPosHTML(view('certificates.footer', $data + ['side' => 'left'])->render(), 24, 158, 80, 34, 'hidden');
            $mpdf->WriteFixedPosHTML(view('certificates.footer', $data + ['side' => 'right'])->render(), 193, 158, 80, 34, 'hidden');
        }

        return $mpdf->Output('', Destination::STRING_RETURN);
    }

    /** Pennant garland like the one strung over the mandal's pandal, as an SVG data URI. */
    private function buntingDataUri(): string
    {
        $flags = '';

        for ($i = 0; $i < 27; $i++) {
            $x = $i * 10 + 0.5;
            $fill = $i % 2 === 0 ? '#C13515' : '#E8912B';
            $flags .= sprintf('<polygon points="%.1f,0.8 %.1f,0.8 %.1f,11.5" fill="%s"/>', $x, $x + 9, $x + 4.5, $fill);
        }

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 270 12" width="270" height="12">'
            .'<line x1="0" y1="0.8" x2="270" y2="0.8" stroke="#B8892E" stroke-width="0.8"/>'
            .$flags
            .'</svg>';

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
