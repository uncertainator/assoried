<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $consultation->titre }} — Fiche terrain</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Georgia, serif; font-size: 13pt; color: #1a1a1a; padding: 20mm; }
        h1 { font-size: 20pt; font-weight: 700; margin-bottom: 6pt; }
        h2 { font-size: 13pt; font-weight: 600; margin: 16pt 0 8pt; border-bottom: 1px solid #ccc; padding-bottom: 4pt; }
        .meta { font-size: 11pt; color: #555; margin-bottom: 12pt; }
        .description { border: 1px solid #ccc; border-radius: 4pt; padding: 10pt; margin-bottom: 16pt; font-size: 11pt; line-height: 1.6; }
        .print-btn { display: inline-block; margin-bottom: 20px; padding: 8px 16px; background: #1a1a1a; color: #fff; border: none; border-radius: 6px; font-size: 14px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; margin-top: 8pt; }
        th { text-align: left; padding: 6pt 8pt; background: #f0f0f0; border: 1pt solid #ccc; font-size: 11pt; }
        td { padding: 8pt 8pt; border: 1pt solid #ccc; height: 24pt; }
        .ligne-avis { border: 1pt solid #ccc; height: 14mm; margin-bottom: 6pt; }
        .options-list { display: flex; flex-direction: column; gap: 8pt; margin-top: 8pt; }
        .option-item { display: flex; align-items: center; gap: 8pt; padding: 6pt; border: 1pt solid #ccc; }
        .checkbox { width: 12pt; height: 12pt; border: 1pt solid #555; display: inline-block; }
        @media print {
            .print-btn { display: none; }
            body { padding: 15mm; }
        }
        @media screen {
            body { max-width: 800px; margin: 0 auto; padding: 40px 24px; }
        }
    </style>
</head>
<body>

    <button class="print-btn" onclick="window.print()">🖨 Imprimer cette fiche</button>

    <div class="meta">Consultation publique — Fiche terrain</div>
    <h1>{{ $consultation->titre }}</h1>

    <div class="meta">
        Mode : {{ $consultation->mode_recueil->label() }}
        @if ($consultation->date_cloture)
            &nbsp;·&nbsp; Date de clôture : {{ $consultation->date_cloture->translatedFormat('j M Y') }}
        @endif
    </div>

    @if ($consultation->description)
        <div class="description">{{ $consultation->description }}</div>
    @endif

    @if ($consultation->mode_recueil->value === 'avis_libre')
        <h2>Zone de réponse manuscrite</h2>
        <p style="font-size:11pt;color:#555;margin-bottom:10pt;">Chaque participant inscrit son avis dans un encadré.</p>
        @for ($i = 1; $i <= 8; $i++)
            <div style="margin-bottom:10pt;">
                <div style="font-size:10pt;color:#777;margin-bottom:3pt;">Réponse {{ $i }}</div>
                <div class="ligne-avis"></div>
                <div class="ligne-avis"></div>
            </div>
        @endfor

    @elseif ($consultation->mode_recueil->value === 'signature')
        <h2>Liste des signatures</h2>
        <table>
            <thead>
                <tr>
                    <th style="width:50%;">Prénom</th>
                    <th style="width:50%;">Nom</th>
                </tr>
            </thead>
            <tbody>
                @for ($i = 0; $i < 20; $i++)
                    <tr><td></td><td></td></tr>
                @endfor
            </tbody>
        </table>

    @elseif ($consultation->mode_recueil->value === 'vote_indicatif')
        <h2>Vote indicatif</h2>
        <p style="font-size:11pt;color:#555;margin-bottom:10pt;">Chaque participant coche son choix.</p>
        <table>
            <thead>
                <tr>
                    <th style="width:40%;">Prénom / Nom</th>
                    @foreach ($consultation->options ?? [] as $option)
                        <th>{{ $option }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @for ($i = 0; $i < 20; $i++)
                    <tr>
                        <td></td>
                        @foreach ($consultation->options ?? [] as $option)
                            <td style="text-align:center;">☐</td>
                        @endforeach
                    </tr>
                @endfor
            </tbody>
        </table>
    @endif

</body>
</html>
