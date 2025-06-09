<?php

declare(strict_types=1);

namespace TimWithers\LaravelHorizonClearAll\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redis;
use Laravel\Horizon\RedisQueue;
use Laravel\Horizon\Repositories\RedisJobRepository;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'horizon:clear-all')]
final class HorizonClearAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'horizon:clear-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all of the jobs from all defined queues';

    /**
     * Execute the console command.
     */
    use ConfirmableTrait;

    /**
     * Execute the console command.
     */
    public function handle(RedisJobRepository $jobRepository, QueueManager $manager): ?int
    {
        if (! $this->confirmToProceed()) {
            return 1;
        }

        if (! method_exists(RedisQueue::class, 'clear')) {
            $this->line('<error>Clearing queues is not supported on this version of Laravel</error>');

            return 1;
        }

        $connection = Arr::first($this->laravel['config']->get('horizon.defaults'))['connection'] ?? 'redis';

        $queues = [$this->laravel['config']->get("queue.connections.{$connection}.queue", 'default')];

        $supervisors = $this->laravel['config']->get('horizon.defaults', []);
        foreach ($supervisors as $supervisor) {
            $queues = array_merge($queues, Arr::wrap($supervisor['queue'] ?? []));
        }

        $queues = array_values(array_unique($queues));

        foreach ($queues as $queue) {
            $jobRepository->purge($queue);

            $count = $manager->connection($connection)->clear($queue);

            $this->line('<info>Cleared '.$count.' '.str('job')->plural($count).' from the ['.$queue.'] queue</info>');
        }

        $uniqueJobKeys = Redis::keys('laravel_unique_job*');
        foreach ($uniqueJobKeys as $key) {
            Redis::del(str($key)->after(config('database.redis.options.prefix')));
        }
        $this->line('<info>Cleared '.count($uniqueJobKeys).' job keys</info>');

        return 0;
    }
}
