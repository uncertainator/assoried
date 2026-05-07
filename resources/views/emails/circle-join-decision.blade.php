<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Réponse à votre demande d'inscription</title>
</head>
<body style="margin:0;padding:0;background:#fdfaf3;font-family:'Public Sans',system-ui,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#fdfaf3;padding:40px 20px;">
  <tr>
    <td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

        <tr>
          <td style="background:#8e3217;border-radius:14px 14px 0 0;padding:32px 40px;text-align:left;">
            <div style="font-family:Georgia,serif;font-size:22px;font-weight:600;color:#fdfaf3;">La Fabrique</div>
            <div style="font-size:12px;color:#e89a78;margin-top:4px;letter-spacing:.08em;text-transform:uppercase;">Association citoyenne · Alsace</div>
          </td>
        </tr>

        <tr>
          <td style="background:#ffffff;padding:40px;border-left:1px solid #efe4cb;border-right:1px solid #efe4cb;">
            @if ($membership->status->value === 'approved')
              <p style="font-family:Georgia,serif;font-size:24px;line-height:1.25;color:#1d1a10;margin:0 0 16px;">
                Bienvenue dans le cercle !
              </p>
              <p style="font-size:16px;line-height:1.6;color:#2b2517;margin:0 0 24px;">
                Votre demande d'inscription au cercle <strong>{{ $membership->circle->name }}</strong> a été <strong style="color:#3a7d44;">acceptée</strong>.
                Vous faites désormais partie du groupe.
              </p>
            @else
              <p style="font-family:Georgia,serif;font-size:24px;line-height:1.25;color:#1d1a10;margin:0 0 16px;">
                Demande non retenue
              </p>
              <p style="font-size:16px;line-height:1.6;color:#2b2517;margin:0 0 24px;">
                Votre demande d'inscription au cercle <strong>{{ $membership->circle->name }}</strong> n'a pas pu être acceptée pour le moment.
              </p>
              @if ($membership->rejection_reason)
                <div style="background:#fdf4f1;border-left:3px solid #c85226;padding:16px 20px;margin-bottom:24px;border-radius:0 8px 8px 0;">
                  <p style="font-size:14px;color:#2b2517;margin:0;line-height:1.6;">
                    <strong>Motif :</strong> {{ $membership->rejection_reason }}
                  </p>
                </div>
              @endif
              <p style="font-size:14px;color:#6f6553;margin:0;">
                Vous pouvez soumettre une nouvelle demande depuis votre espace adhérent.
              </p>
            @endif
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
