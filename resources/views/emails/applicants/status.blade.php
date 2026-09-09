@php
    $brand = $brand ?? \App\Support\CareerBrand::brand(null);
    $statusValue = (int) ($statusValue ?? data_get($applicant ?? null, 'status.value', 0));
    $applicantName = $applicantName ?? data_get($applicant ?? null, 'full_name', 'Kandidat');
    $positionName = $positionName ?? data_get($applicant ?? null, 'jobVacancy.name', 'Posisi yang dilamar');
    $submittedAt = $submittedAt ?? data_get($applicant ?? null, 'created_at');
    $contactEmail = $contactEmail ?? $brand['email'];
    $logoSource = $brand['logo_url'] ?? asset($brand['logo']);
    $primaryColor = $brand['primary_color'] ?? '#304767';
    $accentColor = $brand['accent_color'] ?? '#2563eb';
    $headerBackgroundColor = $brand['header_background_color'] ?? $primaryColor;
    $headerTextColor = $brand['header_text_color'] ?? '#ffffff';

    $content = match ($statusValue) {
        1 => [
            'label' => 'Interview',
            'title' => 'Anda Diundang Interview',
            'intro' => 'Lamaran Anda telah kami review dan Anda masuk ke tahap interview.',
            'message' => 'Mohon mempersiapkan diri dengan baik. Tim rekrutmen akan menghubungi Anda untuk konfirmasi teknis interview.',
            'badgeColor' => $accentColor,
        ],
        2 => [
            'label' => 'Diterima',
            'title' => 'Selamat, Anda Diterima',
            'intro' => 'Dengan senang hati kami informasikan bahwa Anda diterima untuk melanjutkan proses bersama '.$brand['name'].'.',
            'message' => 'Tim kami akan menghubungi Anda untuk informasi berikutnya terkait administrasi dan jadwal bergabung.',
            'badgeColor' => '#16a34a',
        ],
        default => [
            'label' => 'Submitted',
            'title' => 'Lamaran Anda Sudah Terkirim',
            'intro' => 'Terima kasih, lamaran Anda telah berhasil kami terima.',
            'message' => 'Mohon menunggu proses review dari tim rekrutmen kami. Jika profil Anda sesuai dengan kebutuhan posisi, kami akan menghubungi Anda untuk tahap interview.',
            'badgeColor' => $accentColor,
        ],
    };

    $steps = [
        ['value' => 0, 'label' => 'Submitted', 'description' => 'Lamaran diterima'],
        ['value' => 1, 'label' => 'Interview', 'description' => 'Seleksi lanjutan'],
        ['value' => 2, 'label' => 'Diterima', 'description' => 'Hasil akhir'],
    ];
@endphp

<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $content['title'] }}</title>
</head>

<body style="margin: 0; padding: 0; background: #eef2f7; font-family: Arial, Helvetica, sans-serif; color: #172033;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background: #eef2f7; padding: 28px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 640px; background: #ffffff; border-collapse: collapse;">
                    <tr>
                        <td style="background: {{ $headerBackgroundColor }}; padding: 28px 32px; border-bottom: 4px solid {{ $primaryColor }};">
                            <img src="{{ $logoSource }}" alt="{{ $brand['name'] }}" width="132" style="display: block; max-width: 132px; height: auto;">
                            <p style="margin: 22px 0 6px; color: {{ $accentColor }}; font-size: 13px; line-height: 1.5; font-weight: 700;">Status Lamaran</p>
                            <h1 style="margin: 0; color: {{ $headerTextColor }}; font-size: 26px; line-height: 1.25; font-weight: 700;">{{ $content['title'] }}</h1>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 30px 32px 20px;">
                            <p style="margin: 0; font-size: 16px; line-height: 1.7;">Halo {{ $applicantName }},</p>
                            <p style="margin: 12px 0 0; font-size: 16px; line-height: 1.7;">{{ $content['intro'] }}</p>
                            <p style="margin: 12px 0 0; font-size: 15px; line-height: 1.7; color: #48566d;">{{ $content['message'] }}</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 0 32px 24px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border: 1px solid #dbe3ee; border-collapse: collapse;">
                                <tr>
                                    <td style="padding: 16px 18px; border-bottom: 1px solid #dbe3ee; background: #f8fafc;">
                                        <p style="margin: 0; color: #64748b; font-size: 12px;">Posisi Dilamar</p>
                                        <p style="margin: 6px 0 0; color: #172033; font-size: 15px; font-weight: 700;">{{ $positionName }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 16px 18px; border-bottom: 1px solid #dbe3ee;">
                                        <p style="margin: 0; color: #64748b; font-size: 12px;">Tanggal Submit</p>
                                        <p style="margin: 6px 0 0; color: #172033; font-size: 15px; font-weight: 700;">
                                            {{ $submittedAt ? \Illuminate\Support\Carbon::parse($submittedAt)->translatedFormat('d F Y, H:i') : '-' }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 0 32px 28px;">
                            <h2 style="margin: 0 0 16px; font-size: 18px; color: #172033;">Track Record Lamaran</h2>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse: collapse;">
                                @foreach ($steps as $index => $step)
                                    @php
                                        $isDone = $statusValue > $step['value'];
                                        $isCurrent = $statusValue === $step['value'];
                                        $circleBackground = $isDone || $isCurrent ? $primaryColor : '#e2e8f0';
                                        $circleColor = $isDone || $isCurrent ? '#ffffff' : '#64748b';
                                        $lineColor = $statusValue > $step['value'] ? $primaryColor : '#e2e8f0';
                                    @endphp

                                    <tr>
                                        <td width="34" valign="top" style="padding: 0;">
                                            <div style="width: 26px; height: 26px; border-radius: 50%; background: {{ $circleBackground }}; color: {{ $circleColor }}; text-align: center; line-height: 26px; font-size: 13px; font-weight: 700;">
                                                @if ($isDone)
                                                    &#10003;
                                                @else
                                                    {{ $index + 1 }}
                                                @endif
                                            </div>
                                            @if (! $loop->last)
                                                <div style="width: 2px; height: 34px; margin-left: 12px; background: {{ $lineColor }};"></div>
                                            @endif
                                        </td>
                                        <td valign="top" style="padding: 2px 0 18px;">
                                            <p style="margin: 0; font-size: 15px; color: #172033; font-weight: 700;">
                                                {{ $step['label'] }}
                                                @if ($isCurrent)
                                                    <span style="color: {{ $content['badgeColor'] }}; font-size: 12px; font-weight: 700;">Sedang berjalan</span>
                                                @endif
                                            </p>
                                            <p style="margin: 5px 0 0; color: #64748b; font-size: 13px; line-height: 1.5;">{{ $step['description'] }}</p>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 22px 32px; background: #f8fafc; border-top: 1px solid #dbe3ee;">
                            <p style="margin: 0; color: #48566d; font-size: 13px; line-height: 1.7;">
                                Jika ada pertanyaan, silakan hubungi kami melalui
                                <a href="mailto:{{ $contactEmail }}" style="color: {{ $accentColor }}; text-decoration: none;">{{ $contactEmail }}</a>.
                            </p>
                            <p style="margin: 16px 0 0; color: #94a3b8; font-size: 12px; line-height: 1.6;">
                                Email ini dikirim otomatis oleh sistem rekrutmen {{ $brand['name'] }}. Mohon tidak membalas langsung email ini.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
