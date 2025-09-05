<?php

arch('no debugging statements')
    ->expect(['dd', 'dump', 'var_dump', 'ray', 'die'])
    ->not->toBeUsed();

arch('strict types are used')
    ->expect('CanvasLMS\Laravel')
    ->not->toUse(['mixed']);

arch('service provider is correctly structured')
    ->expect('CanvasLMS\Laravel\CanvasServiceProvider')
    ->toExtend('Illuminate\Support\ServiceProvider')
    ->toHaveMethod('register')
    ->toHaveMethod('boot');

arch('facades extend laravel facade')
    ->expect('CanvasLMS\Laravel\Facades')
    ->toExtend('Illuminate\Support\Facades\Facade');

arch('commands extend laravel command')
    ->expect('CanvasLMS\Laravel\Commands')
    ->toExtend('Illuminate\Console\Command');

arch('testing utilities are in testing namespace')
    ->expect('CanvasLMS\Laravel\Testing')
    ->toOnlyBeUsedIn('CanvasLMS\Laravel\Tests');
