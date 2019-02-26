<?php

namespace SsnTestKit\Tests\Server;

function render(string $template, array $data = [])
{
    extract($data, EXTR_SKIP);

    ob_start();

    include __DIR__ . "/views/{$template}";

    $html = ob_get_contents();
    ob_end_clean();

    return $html;
}

function getStatusList()
{
    return [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        103 => 'Early Hints',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Too Early',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    ];
}

function getStatusTextFor($status)
{
    $texts = getStatusList();

    if (! isset($texts[$status])) {
        throw new \InvalidArgumentException("No text found for status {$status}");
    }

    return $texts[$status];
}

function renderStatus($status)
{
    $text = getStatusTextFor($status);

    header("HTTP/1.1 {$status} {$text}", true, $status);
    echo render('status-code.php', ['status' => $status]);
}

switch ($_SERVER['REQUEST_URI']) {
    case '/':
        setcookie('testcookie', 'testcookievalue', time() + 1);
        header('X-Apples: Bananas');

        echo render('home.php');
        break;

    case '/status-informational':
        renderStatus(100);
        break;

    case '/status-successful':
        renderStatus(201);
        break;

    case '/status-redirection':
        header('Location: http://localhost');
        break;

    case '/status-client-error':
        renderStatus(401);
        break;

    case '/status-server-error':
        renderStatus(500);
        break;

    case '/status-ok':
        renderStatus(200);
        break;

    case '/status-forbidden':
        renderStatus(403);
        break;

    case '/status-not-found':
        renderStatus(404);
        break;

    case '/js-delayed-visibility':
        echo render('js-delayed-visibility.php');
        break;
    case '/js-dom-mod':
        echo render('js-dom-mod.php');
        break;
    case '/status-200':
        renderStatus(200);
        break;
    default:
        renderStatus(404);
        break;
}
