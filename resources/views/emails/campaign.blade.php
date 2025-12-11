@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\URL;
    use Illuminate\Support\Str;

    $resolveImage = function ($path) {
        if (!$path) {
            return null;
        }
        return Str::startsWith($path, ['http://', 'https://'])
            ? $path
            : URL::to(Storage::url($path));
    };

    $heroUrl = $resolveImage($campaign->hero_image);
    $tokenMap = [
        '[[email]]' => $recipientEmail ?? '',
        '[[email_url]]' => isset($recipientEmail) ? urlencode($recipientEmail) : '',
    ];
    $renderCopy = function ($value) use ($tokenMap) {
        if ($value === null) {
            return '';
        }
        return strtr($value, $tokenMap);
    };
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $campaign->subject }}</title>
</head>

<body style="margin:0;padding:0;background-color:#0b0d17;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#0b0d17;padding:32px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 12px 40px rgba(9,12,36,0.25);">
                    <tr>
                        <td style="background:linear-gradient(135deg,#0f172a,#111c44);padding:32px;text-align:center;">
                            <img src="{{ asset('frontend/assets/images/logos/logo.png') }}" alt="Forward Edge" width="160" style="display:block;margin:0 auto 16px;">
                            <p style="color:#9fb4ff;margin:0;font-size:12px;text-transform:uppercase;letter-spacing:2px;">Forward Edge Communications</p>
                            <h1 style="color:#ffffff;margin:12px 0 8px;font-size:26px;font-weight:700;">{{ $campaign->title }}</h1>
                            @if($campaign->subtitle)
                                <p style="color:#d8e1ff;margin:0;font-size:16px;">{{ $campaign->subtitle }}</p>
                            @endif
                        </td>
                    </tr>

                    @if($heroUrl)
                        <tr>
                            <td style="padding:0;">
                                <img src="{{ $heroUrl }}" alt="Hero" style="width:100%;display:block;max-height:320px;object-fit:cover;">
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <td style="padding:32px;">
                            <p style="margin:0 0 16px;color:#475467;font-size:15px;line-height:1.6;">
                                @if($recipientName)
                                    Hi {{ $recipientName }},
                                @else
                                    Hi there,
                                @endif
                            </p>

                            @if($campaign->intro)
                                <p style="margin:0 0 24px;color:#1d2939;font-size:16px;line-height:1.7;">{!! nl2br(e($renderCopy($campaign->intro))) !!}</p>
                            @endif

                            @foreach($campaign->blocks ?? [] as $block)
                                @if(($block['type'] ?? '') === 'text')
                                    <div style="margin-bottom:32px;">
                                        @if(!empty($block['heading']))
                                            <h2 style="margin:0 0 12px;font-size:20px;color:#0f172a;">{{ $renderCopy($block['heading']) }}</h2>
                                        @endif
                                        @if(!empty($block['body']))
                                            <p style="margin:0;color:#475467;font-size:15px;line-height:1.7;">{!! nl2br(e($renderCopy($block['body']))) !!}</p>
                                        @endif
                                    </div>
                                @elseif(($block['type'] ?? '') === 'list')
                                    <div style="margin-bottom:32px;padding:20px;border:1px solid #e4e7ec;border-radius:12px;background-color:#f8f9ff;">
                                        @if(!empty($block['heading']))
                                            <h3 style="margin:0 0 12px;font-size:18px;color:#0f172a;">{{ $renderCopy($block['heading']) }}</h3>
                                        @endif
                                        @if(!empty($block['body']))
                                            <p style="margin:0 0 16px;color:#475467;font-size:15px;line-height:1.7;">{!! nl2br(e($renderCopy($block['body']))) !!}</p>
                                        @endif
                                        @if(!empty($block['items']))
                                            <ul style="padding-left:20px;margin:0;color:#101828;line-height:1.7;">
                                                @foreach($block['items'] as $item)
                                                    <li style="margin-bottom:8px;">{{ $renderCopy($item) }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                @elseif(($block['type'] ?? '') === 'image')
                                    @php $blockImage = $resolveImage($block['image_url'] ?? null); @endphp
                                    @if($blockImage)
                                    <div style="margin-bottom:32px;text-align:center;">
                                            <img src="{{ $blockImage }}" alt="{{ $block['alt'] ?? 'Campaign asset' }}" style="width:100%;max-height:320px;object-fit:cover;border-radius:12px;">
                                        @if(!empty($block['caption']))
                                            <p style="margin:12px 0 0;color:#667085;font-size:13px;">{{ $renderCopy($block['caption']) }}</p>
                                        @endif
                                    </div>
                                    @endif
                                @elseif(($block['type'] ?? '') === 'cards')
                                    <div style="margin-bottom:32px;">
                                        @if(!empty($block['heading']))
                                            <h3 style="margin:0 0 12px;font-size:19px;color:#0f172a;">{{ $renderCopy($block['heading']) }}</h3>
                                        @endif
                                        @if(!empty($block['body']))
                                            <p style="margin:0 0 16px;color:#475467;">{!! nl2br(e($renderCopy($block['body']))) !!}</p>
                                        @endif
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                @foreach($block['cards'] ?? [] as $card)
                                                    <td style="width:33%;padding:8px;vertical-align:top;">
                                                        <div style="border:1px solid #e4e7ec;border-radius:12px;padding:16px;height:100%;background:#ffffff;">
                                                            @php $cardImage = $resolveImage($card['image'] ?? null); @endphp
                                                            @if($cardImage)
                                                                <img src="{{ $cardImage }}" alt="" style="width:100%;border-radius:8px;margin-bottom:12px;max-height:140px;object-fit:cover;">
                                                            @endif
                                                            @if(!empty($card['title']))
                                                                <h4 style="margin:0 0 8px;font-size:16px;color:#0f172a;">{{ $renderCopy($card['title']) }}</h4>
                                                            @endif
                                                            @if(!empty($card['body']))
                                                                <p style="margin:0;color:#475467;font-size:14px;line-height:1.6;">{!! nl2br(e($renderCopy($card['body']))) !!}</p>
                                                            @endif
                                                        </div>
                                                    </td>
                                                @endforeach
                                            </tr>
                                        </table>
                                    </div>
                                @endif
                            @endforeach

                            @php
                                $finalCta = $ctaLink ?? $campaign->cta_link;
                            @endphp
                            @if($campaign->cta_text && $finalCta)
                                <div style="text-align:center;margin:32px 0;">
                                    <a href="{{ $finalCta }}" style="background-color:#f97316;color:#ffffff;text-decoration:none;padding:14px 32px;border-radius:999px;font-weight:600;display:inline-block;">
                                        {{ $campaign->cta_text }}
                                    </a>
                                </div>
                            @endif

                            <p style="margin:24px 0 0;color:#98a2b3;font-size:13px;text-align:center;">
                                Forward Edge Consulting LTD<br>
                                You are receiving this email because you are connected with Forward Edge programs.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
