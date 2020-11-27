<?php

namespace App\Tags;

use \Carbon\Carbon;
use \Statamic\Tags\Tags;
use \Illuminate\Support\Facades\Cache;
use \Illuminate\Support\Facades\Http;
use \Illuminate\Support\Facades\Log;

class Meetup extends Tags
{
    /**
     * The {{ meetup }} tag.
     *
     * @return string|array
     */
    public function index()
    {
        $group = $this->params->get('group', 'Norfolk-Developers-NorDev');
        $limit = $this->params->get('limit', 9);
        $key = "meetup-events-$group-$limit";

        if ($cache = Cache::get($key)) {
            return $cache;
        }

        $response = Http::get('https://cors-it-is.shaun.now.sh/', [
            'url' => "https://api.meetup.com/$group/events",
            'sign' => true,
            'photo-host' => 'public',
            'page' => 1,
            'limit' => $limit
        ]);

        if (! $response->ok()) {
            Log::error('Error fetching events from Meetup API.');
            return [];
        }

        $events = collect($response->json())
            ->sortBy('time')
            ->take($limit)
            ->map(function ($event) {
                $date = $event['local_date'];
                $time = $event['local_time'];
                $tz = $event['group']['timezone'];

                $event['start'] = Carbon::createFromFormat('Y-m-d H:i', "$date $time", $tz);
                $event['end'] = Carbon::createFromFormat('Y-m-d H:i', "$date $time", $tz)
                    ->addMilliseconds($event['duration']);

                return $event;
            });

        Cache::put($key, $events, now()->secondsUntilEndOfDay());

        return $events;
    }
}
