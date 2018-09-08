<?php

namespace DevDavido\Reporto\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

class LogReportController extends Controller
{
    /**
     * Only allow certain request content types and log non-empty error reports to error log.
     *
     * Example request:
     * ```bash
     * curl -d '[{ "type": "csp", "age": 10, "url": "https://example.com/vulnerable-page/", \
     * "user_agent": "Mozilla/5.0 Firefox/60.0", "body": { "blocked": "https://evil.com/evil.js", \
     * "directive": "script-src", "status": 200, "referrer": "https://evil.com/" }}]' \
     * -X POST -H "Content-Type: application/reports+json" https://domain.localhost/log/network-errors/
     * ```
     * @throws NotAcceptableHttpException
     * @throws BadRequestHttpException
     * @param Request $request
     * @return void
     */
    public function handle(Request $request)
    {
        $allowedContentTypes = ['application/json', 'application/csp-report', 'application/reports+json'];
        abort_unless(in_array(mb_strtolower($request->header('Content-Type')), $allowedContentTypes), 406);

        $payload = $request->json();
        abort_unless($payload->count(), 400);

        Log::error('Report API report:', $payload->all());
    }
}