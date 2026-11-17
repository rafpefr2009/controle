<?php

// Escapa qualquer saída de texto para HTML.
function e($texto)
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

// Redireciona e encerra a execução.
function redirecionar($url)
{
    header('Location: ' . $url);
    exit;
}
