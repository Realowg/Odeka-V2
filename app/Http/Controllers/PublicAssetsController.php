<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class PublicAssetsController extends Controller
{
    protected function resolveAsset(string $source, ?string $url, ?string $file)
    {
        if ($source === 'url' && $url) {
            return redirect()->away($url);
        }
        if ($file) {
            $disk = config('filesystems.default');
            if (in_array($disk, ['s3','dospace','wasabi','backblaze','vultr','pushr'])) {
                return redirect(Storage::disk($disk)->temporaryUrl($file, now()->addMinutes(5)));
            }
            return Storage::disk($disk)->download($file);
        }
        abort(404);
    }

    public function mediaKit()
    {
        return $this->resolveAsset(
            config('settings.media_kit_source'),
            config('settings.media_kit_url'),
            config('settings.media_kit_file')
        );
    }

    public function caseStudy()
    {
        return $this->resolveAsset(
            config('settings.case_study_source'),
            config('settings.case_study_url'),
            config('settings.case_study_file')
        );
    }

    public function oshowSponsorKit()
    {
        return $this->resolveAsset(
            config('settings.oshow_sponsorship_pdf_source'),
            config('settings.oshow_sponsorship_pdf_url'),
            config('settings.oshow_sponsorship_pdf_file')
        );
    }
}


