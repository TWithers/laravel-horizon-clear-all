<?php

declare(strict_types=1);

namespace TimWithers\LaravelHorizonClearAll;

use Illuminate\Support\ServiceProvider;
use TimWithers\LaravelHorizonClearAll\Commands\HorizonClearAll;

/**
 * @internal
 */
final class LaravelHorizonClearAllServiceProvider extends ServiceProvider
{
    /**
     * {@inheritDoc}
     */
    public function boot(): void
    {
        $this->registerCommands();
    }

    /**
     * {@inheritDoc}
     */
    private function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                HorizonClearAll::class,
            ]);
        }
    }
}
