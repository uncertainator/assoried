<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Votre demande d'adhésion</title>
</head>
<body style="margin:0;padding:0;background:#fdfaf3;font-family:'Public Sans',system-ui,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#fdfaf3;padding:40px 20px;">
  <tr>
    <td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

        {{-- Header --}}
        <tr>
          <td style="background:#8e3217;border-radius:14px 14px 0 0;padding:32px 40px;text-align:left;">
            <div style="font-family:Georgia,serif;font-size:22px;font-weight:600;color:#fdfaf3;">
              La Fabrique
            </div>
            <div style="font-size:12px;color:#e89a78;margin-top:4px;letter-spacing:.08em;text-transform:uppercase;">
              Association citoyenne · Alsace
            </div>
          </td>
        </tr>

        {{-- Body --}}
        <tr>
          <td style="background:#ffffff;padding:40px;border-left:1px solid #efe4cb;border-right:1px solid #efe4cb;">
            <p style="font-family:Georgia,serif;font-size:28px;line-height:1.25;color:#1d1a10;margin:0 0 16px;">
              Votre demande d'adhésion
            </p>
            <p style="font-size:16px;line-height:1.6;color:#2b2517;margin:0 0 16px;">
              Après examen par le bureau, votre demande d'adhésion à La Fabrique
              n'a pas pu être retenue.
            </p>
            @if (! empty($reason))
            <div style="background:#f8f1e1;border:1px solid #efe4cb;border-radius:8px;padding:16px 20px;margin:0 0 16px;">
              <p style="font-size:13px;color:#6f6553;margin:0 0 6px;text-transform:uppercase;letter-spacing:.06em;">
                Motif
              </p>
              <p style="font-size:15px;line-height:1.6;color:#2b2517;margin:0;">
                {{ $reason }}
              </p>
            </div>
            @endif
            <p style="font-size:16px;line-height:1.6;color:#2b2517;margin:0;">
              Pour toute question, vous pouvez répondre à cet email.
            </p>
          </td>
        </tr>

        {{-- Footer --}}
        <tr>
          <td style="background:#f8f1e1;border:1px solid #efe4cb;border-top:none;border-radius:0 0 14px 14px;padding:20px 40px;text-align:center;">
            <p style="font-size:12px;color:#6f6553;margin:0;">
              © {{ date('Y') }} La Fabrique · Association citoyenne · Alsace
            </p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>
</body>
</html>
