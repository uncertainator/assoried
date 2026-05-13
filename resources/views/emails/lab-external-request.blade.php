<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Nouvelle demande externe Lab</title>
</head>
<body style="margin:0;padding:0;background:#fdfaf3;font-family:'Public Sans',system-ui,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#fdfaf3;padding:40px 20px;">
  <tr>
    <td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

        <tr>
          <td style="background:#8e3217;border-radius:14px 14px 0 0;padding:32px 40px;text-align:left;">
            <div style="font-family:Georgia,serif;font-size:22px;font-weight:600;color:#fdfaf3;">La Fabrique</div>
            <div style="font-size:12px;color:#e89a78;margin-top:4px;letter-spacing:.08em;text-transform:uppercase;">Lab · Demande externe</div>
          </td>
        </tr>

        <tr>
          <td style="background:#ffffff;padding:40px;border-left:1px solid #efe4cb;border-right:1px solid #efe4cb;">
            <p style="font-family:Georgia,serif;font-size:24px;line-height:1.25;color:#1d1a10;margin:0 0 16px;">
              Nouvelle demande — {{ $externalRequest->type === 'citoyen' ? 'Programme citoyen' : 'Programme entreprise' }}
            </p>
            <p style="font-size:16px;line-height:1.6;color:#2b2517;margin:0 0 24px;">
              Une nouvelle demande externe vient d'être soumise au Lab.
            </p>
            <table cellpadding="0" cellspacing="0" style="background:#fdfaf3;border:1px solid #efe4cb;border-radius:8px;padding:16px 20px;margin-bottom:24px;width:100%;">
              <tr>
                <td style="font-size:13px;color:#6f6553;padding-bottom:8px;white-space:nowrap;padding-right:16px;">Type</td>
                <td style="font-size:14px;color:#1d1a10;padding-bottom:8px;">{{ $externalRequest->type === 'citoyen' ? 'Programme citoyen' : 'Programme entreprise' }}</td>
              </tr>
              <tr>
                <td style="font-size:13px;color:#6f6553;padding-bottom:8px;white-space:nowrap;padding-right:16px;">Contact</td>
                <td style="font-size:14px;color:#1d1a10;padding-bottom:8px;">{{ $externalRequest->nom_contact }}</td>
              </tr>
              @if($externalRequest->raison_sociale)
              <tr>
                <td style="font-size:13px;color:#6f6553;padding-bottom:8px;white-space:nowrap;padding-right:16px;">Entreprise</td>
                <td style="font-size:14px;color:#1d1a10;padding-bottom:8px;">{{ $externalRequest->raison_sociale }}</td>
              </tr>
              @endif
              <tr>
                <td style="font-size:13px;color:#6f6553;padding-bottom:8px;white-space:nowrap;padding-right:16px;">Email</td>
                <td style="font-size:14px;color:#1d1a10;padding-bottom:8px;">{{ $externalRequest->email }}</td>
              </tr>
              @if($externalRequest->telephone)
              <tr>
                <td style="font-size:13px;color:#6f6553;padding-bottom:8px;white-space:nowrap;padding-right:16px;">Téléphone</td>
                <td style="font-size:14px;color:#1d1a10;padding-bottom:8px;">{{ $externalRequest->telephone }}</td>
              </tr>
              @endif
              @if($externalRequest->territoire)
              <tr>
                <td style="font-size:13px;color:#6f6553;padding-bottom:8px;white-space:nowrap;padding-right:16px;">Territoire</td>
                <td style="font-size:14px;color:#1d1a10;padding-bottom:8px;">{{ $externalRequest->territoire }}</td>
              </tr>
              @endif
              @if($externalRequest->besoin_type)
              <tr>
                <td style="font-size:13px;color:#6f6553;padding-bottom:8px;white-space:nowrap;padding-right:16px;">Type de besoin</td>
                <td style="font-size:14px;color:#1d1a10;padding-bottom:8px;">{{ ucfirst($externalRequest->besoin_type) }}</td>
              </tr>
              @endif
            </table>
            <div style="background:#fdfaf3;border:1px solid #efe4cb;border-radius:8px;padding:16px 20px;margin-bottom:32px;">
              <div style="font-size:13px;color:#6f6553;margin-bottom:6px;">Message</div>
              <div style="font-size:14px;color:#1d1a10;line-height:1.6;">{{ $externalRequest->message }}</div>
            </div>
            <div style="text-align:center;margin:32px 0;">
              <a href="{{ $link }}"
                 style="display:inline-block;background:#c85226;color:#fdfaf3;font-size:15px;font-weight:500;padding:14px 32px;border-radius:8px;text-decoration:none;">
                Voir les demandes externes →
              </a>
            </div>
            <p style="font-size:13px;color:#6f6553;margin:24px 0 0;line-height:1.5;">
              Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :<br>
              <span style="word-break:break-all;color:#b1411c;">{{ $link }}</span>
            </p>
          </td>
        </tr>

        <tr>
          <td style="background:#f8f1e1;border:1px solid #efe4cb;border-top:none;border-radius:0 0 14px 14px;padding:20px 40px;text-align:center;">
            <p style="font-size:12px;color:#6f6553;margin:0;">© {{ date('Y') }} La Fabrique · Association citoyenne · Alsace</p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>
</body>
</html>
