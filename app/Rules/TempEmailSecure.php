<?php

namespace App\Rules;

use Cache;
use GuzzleHttp\Client;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Log;

class TempEmailSecure implements Rule
{
    protected $blacklistedDomains;

    public function __construct()
    {
        $this->blacklistedDomains = Cache::remember('TempEmailBlackListSecure', 60 * 60 * 24, function () {
            try {
                return $this->fetchBlacklistedDomains();
            } catch (\Exception $e) {
                Log::warning('Failed to fetch disposable email domains: ' . $e->getMessage());
                return $this->getLocalBlacklistedDomains();
            }
        });
    }

    /**
     * Securely fetch blacklisted domains from external source
     */
    private function fetchBlacklistedDomains(): array
    {
        $client = new Client([
            'timeout' => 10,
            'connect_timeout' => 5,
            'verify' => true,
            'headers' => [
                'User-Agent' => 'Laravel-Security-App/1.0',
                'Accept' => 'text/plain'
            ],
            'allow_redirects' => [
                'max' => 3,
                'strict' => true,
                'referer' => true,
                'protocols' => ['https']
            ]
        ]);

        $response = $client->get('https://raw.githubusercontent.com/disposable/disposable-email-domains/master/domains.txt');

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Invalid response code: ' . $response->getStatusCode());
        }

        $content = $response->getBody()->getContents();
        
        // Validate content format
        if (empty($content) || strlen($content) > 1024 * 1024) { // Max 1MB
            throw new \Exception('Invalid content size');
        }

        $domains = array_filter(array_map('trim', explode("\n", $content)));
        
        // Validate domains format
        $validDomains = [];
        foreach ($domains as $domain) {
            if (preg_match('/^[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,}$/', $domain) && strlen($domain) <= 255) {
                $validDomains[] = strtolower($domain);
            }
        }

        if (count($validDomains) < 100) { // Sanity check
            throw new \Exception('Too few valid domains found');
        }

        return $validDomains;
    }

    /**
     * Local fallback list of common disposable email domains
     */
    private function getLocalBlacklistedDomains(): array
    {
        return [
            '10minutemail.com',
            '20minutemail.com',
            '30minutemail.com',
            'guerrillamail.com',
            'mailinator.com',
            'temp-mail.org',
            'tempmail.net',
            'throwaway.email',
            'yopmail.com',
            'maildrop.cc',
            'mailnesia.com',
            'trashmail.com',
            'getnada.com',
            'emailondeck.com',
            'fakeinbox.com',
            'dispostable.com',
            'tempail.com',
            'mohmal.com',
            'mailcatch.com',
            'mytemp.email',
            'temporaryemail.net',
            'spam4.me',
            'emailfake.com',
            'sharklasers.com',
            'grr.la',
            'guerrillamail.info',
            'guerrillamail.net',
            'guerrillamail.org',
            'guerrillamailblock.com',
            'pokemail.net',
            'spamgourmet.com',
            'spamavert.com',
            'despam.it',
            'spambox.us',
            'spamfree24.org',
            'spamfree24.de',
            'spamfree24.net',
            'spamfree24.eu',
            'nowmymail.com',
            'isitwetyet.com',
            'mailexpire.com',
            'mailforspam.com',
            'mailmetrash.com',
            'mailscrap.com',
            'meltmail.com',
            'mintemail.com',
            'mytrashmail.com',
            'netmails.net',
            'spambog.com',
            'spambog.de',
            'spambog.ru',
            'spamday.com',
            'spamex.com',
            'spamherelots.com',
            'spamhole.com',
            'spaml.com',
            'spaml.de',
            'spamspot.com',
            'stuffmail.de',
            'supermailer.jp',
            'tempalias.com',
            'tempinbox.com',
            'tempmailaddress.com',
            'tempymail.com',
            'thankyou2010.com',
            'trash2009.com',
            'trashdevil.com',
            'trashemail.de',
            'trashymail.com',
            'tyldd.com',
            'uggsrock.com',
            'wegwerfmail.de',
            'wegwerfmail.net',
            'wegwerfmail.org',
            'wh4f.org',
            'willselfdestruct.com',
            'winemaven.info',
            'wronghead.com',
            'xoxy.net',
            'yuurok.com',
            'zoemail.org'
        ];
    }

    public function passes($attribute, $value)
    {
        if (empty($value) || !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return true; // Let other validation rules handle this
        }

        $emailDomain = strtolower(substr(strrchr($value, "@"), 1));
        
        if (empty($emailDomain)) {
            return true; // Let other validation rules handle this
        }

        return !in_array($emailDomain, $this->blacklistedDomains);
    }

    public function message()
    {
        return __('general.email_valid');
    }
}