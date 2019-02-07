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

switch ($_SERVER['REQUEST_URI']) {
    case '/':
        echo render('home.php');
        break;
    case '/js-delayed-visibility':
        echo render('js-delayed-visibility.php');
        break;
    case '/js-dom-mod':
        echo render('js-dom-mod.php');
        break;
    // @todo Generic status renderer which sends correct status header.
    case '/status-200':
        echo render('status-code.php', ['status' => 200]);
        break;
    default:
        header("HTTP/1.1 404, Not Found", true, 404);

        echo render('status-code.php', ['status' => 404]);
        break;
}
