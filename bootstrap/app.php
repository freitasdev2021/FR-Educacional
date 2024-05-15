<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            "fornecedor" => \App\Http\Middleware\fornecedor::class,
            "secretario" => \App\Http\Middleware\secretario::class,
            "diretor" => \App\Http\Middleware\diretor::class,
            "pedagogo" => \App\Http\Middleware\pedagogo::class,
            "coordenador" => \App\Http\Middleware\coordenador::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
