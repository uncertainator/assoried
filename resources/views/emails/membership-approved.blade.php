<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Adhésion validée</title>
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
              Bienvenue !
            </p>
            <p style="font-size:16px;line-height:1.6;color:#2b2517;margin:0 0 32px;">
              Bonne nouvelle : votre adhésion à La Fabrique a été
              <strong>validée par le bureau</strong>. Vous pouvez désormais vous
              connecter à votre espace adhérent.
            </p>
            <div style="text-align:center;margin:32px 0;">
              <a href="{{ $loginUrl }}"
                 style="display:inline-block;background:#c85226;color:#fdfaf3;font-size:15px;font-weight:500;padding:14px 32px;border-radius:8px;text-decoration:none;">
                Se connecter →
              </a>
            </div>
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
