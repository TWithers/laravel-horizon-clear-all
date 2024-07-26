<?php

namespace Tests\Feature;

use Illuminate\Queue\QueueManager;
use Laravel\Horizon\Contracts\JobRepository;
use Laravel\Horizon\Repositories\RedisJobRepository;
use Tests\IntegrationTest;

class ClearCommandTest extends IntegrationTest
{
    public function test_clear_command_on_all_queues()
    {
        config(['queue.connections.redis.queue' => 'default']);
        config(['horizon.defaults' => [
            'supervisor-1' => ['queue' => ['a', 'b', 'c']],
            'supervisor-2' => ['queue' => ['c', 'd', 'e', 'f']],
            'supervisor-3' => ['queue' => 'foo'],
        ]]);

        $mock = $this->mock(
            RedisJobRepository::class,
            function (\Mockery\MockInterface $mock) {
                $mock->shouldReceive('purge')->times(8)->andReturnTrue();
            }
        );
        $this->app->offsetSet(JobRepository::class, $mock);

        $mock = $this->mock(
            QueueManager::class,
            function (\Mockery\MockInterface $mock) {
                $mock->shouldReceive('connection')->times(8)->andReturnSelf();
                $mock->shouldReceive('clear')->with('default')->times(1)->andReturn(2);
                $mock->shouldReceive('clear')->times(7)->andReturn(1);
            }
        );
        $this->app->offsetSet(QueueManager::class, $mock);

        $this->artisan('horizon:clear-all')
            ->expectsOutputToContain('Cleared 2 jobs from the [default] queue')
            ->expectsOutputToContain('Cleared 1 job from the [a] queue')
            ->expectsOutputToContain('Cleared 1 job from the [b] queue')
            ->expectsOutputToContain('Cleared 1 job from the [c] queue')
            ->expectsOutputToContain('Cleared 1 job from the [d] queue')
            ->expectsOutputToContain('Cleared 1 job from the [e] queue')
            ->expectsOutputToContain('Cleared 1 job from the [f] queue')
            ->expectsOutputToContain('Cleared 1 job from the [foo] queue');
    }
}
