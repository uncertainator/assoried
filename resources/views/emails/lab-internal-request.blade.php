<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Nouvelle demande de soutien Lab</title>
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
            <p style="font-family:Georgia,serif;font-size:24px;line-height:1.25;color:#1d1a10;margin:0 0 16px;">
              Nouvelle demande de soutien
            </p>
            <p style="font-size:16px;line-height:1.6;color:#2b2517;margin:0 0 24px;">
              Le cercle <strong>{{ $labRequest->circle->name }}</strong> a soumis une demande de soutien au Lab.
            </p>
            <table cellpadding="0" cellspacing="0" style="background:#fdfaf3;border:1px solid #efe4cb;border-radius:8px;padding:16px 20px;margin-bottom:24px;width:100%;">
              <tr>
                <td style="font-size:13px;color:#6f6553;padding-bottom:8px;white-space:nowrap;padding-right:16px;">Demandeur</td>
                <td style="font-size:14px;color:#1d1a10;padding-bottom:8px;">{{ $labRequest->user->name ?: $labRequest->user->email }}</td>
              </tr>
              <tr>
                <td style="font-size:13px;color:#6f6553;padding-bottom:8px;white-space:nowrap;padding-right:16px;">Cercle</td>
                <td style="font-size:14px;color:#1d1a10;padding-bottom:8px;">{{ $labRequest->circle->name }}</td>
              </tr>
              @if($labRequest->labService)
              <tr>
                <td style="font-size:13px;color:#6f6553;padding-bottom:8px;white-space:nowrap;padding-right:16px;">Service visé</td>
                <td style="font-size:14px;color:#1d1a10;padding-bottom:8px;">{{ $labRequest->labService->title }}</td>
              </tr>
              @endif
              @if($labRequest->desired_date)
              <tr>
                <td style="font-size:13px;color:#6f6553;padding-bottom:8px;white-space:nowrap;padding-right:16px;">Date souhaitée</td>
                <td style="font-size:14px;color:#1d1a10;padding-bottom:8px;">{{ $labRequest->desired_date->format('d/m/Y') }}</td>
              </tr>
              @endif
            </table>
            <div style="background:#fdfaf3;border:1px solid #efe4cb;border-radius:8px;padding:16px 20px;margin-bottom:32px;">
              <div style="font-size:13px;color:#6f6553;margin-bottom:6px;">Message</div>
              <div style="font-size:14px;color:#1d1a10;line-height:1.6;">{{ $labRequest->message }}</div>
            </div>
            <div style="text-align:center;margin:32px 0;">
              <a href="{{ $link }}"
                 style="display:inline-block;background:#c85226;color:#fdfaf3;font-size:15px;font-weight:500;padding:14px 32px;border-radius:8px;text-decoration:none;">
                Voir les demandes reçues →
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
