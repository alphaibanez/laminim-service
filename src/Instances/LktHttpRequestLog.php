<?php

namespace Lkt\Instances;

use donatj\UserAgent\UserAgentParser;
use Lkt\Factory\Instantiator\Enums\CrudOperation;
use Lkt\Factory\Schemas\Enums\AccessPolicyEndOfLife;
use Lkt\Generated\GeneratedLktHttpRequestLog;
use Lkt\Http\Networking\Networking;
use Lkt\Http\Router;
use function Lkt\Tools\Parse\clearInput;

class LktHttpRequestLog extends GeneratedLktHttpRequestLog
{
    const COMPONENT = 'lkt-http-request-log';

    protected function prepareCrudData(array $data, CrudOperation|null $operation = null): array
    {
        $networking = Networking::getInstance();

        $parser = new UserAgentParser();
        $ua = $parser->parse();

        $route = clearInput($data['route']);
        $method = clearInput($data['method']);
        $responseStatus = (int)$data['responseStatus'];
        $payload = $data['payload'];
        $request = Router::getHTTPRequestHeaders();

        $this->setAccessPolicy('create', AccessPolicyEndOfLife::UntilNextWrite);

        return [
            'createdAt' => time(),
            'route' => $route,
            'method' => $method,
            'payload' => $payload,
            'request' => $request,
            'responseStatus' => $responseStatus,
            'clientProtocol' => $networking->httpProtocol,
            'clientIPAddress' => $networking->remoteAddress,
            'clientUserAgent' => $networking->userAgent,
            'clientBrowser' => clearInput($ua->browser()),
            'clientBrowserVersion' => clearInput($ua->browserVersion()),
            'clientOS' => clearInput($ua->platform()),
        ];
    }
}