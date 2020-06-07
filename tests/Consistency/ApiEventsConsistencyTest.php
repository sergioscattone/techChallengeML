<?php

namespace Tests\Consistency;

use Tests\TestCase;
use App\Models\Event;
use App\Http\Resources\EventResource;
use Carbon\Carbon;

class ApiEventsConsistencyTest extends TestCase
{
    /**
     * @return void
     */
    public function testApiGetEventsConsistencyNoParams()
    {
        $endpoint = '/api/events';
        $events = json_encode(EventResource::collection(Event::get())->resolve());
        $token = str_replace("base64:/", "", env("APP_KEY"));
        $response = $this->withHeaders([
            'token' => $token,
        ])->get($endpoint);
        $respEvents = $response->getContent();
        $this->assertEquals($events, $respEvents);
    }

    /**
     * @return void
     */
    public function testApiGetEventsConsistencyByDate()
    {
        $from = Carbon::now()->subWeek();
        $to = Carbon::now();
        $endpoint = '/api/events';
        $eventDB = Event::where('created_at', '>=', $from)->where('created_at', '<=', $to)->get();
        $events = json_encode(EventResource::collection($eventDB)->resolve());
        $token = str_replace("base64:/", "", env("APP_KEY"));
        $response = $this->call('GET', $endpoint, [
            'token' => $token,
            'from' => $from,
            'to' => $to,
        ]);
        $respEvents = $response->getContent();
        $this->assertEquals($events, $respEvents);
    }

    /**
     * @return void
     */
    public function testApiGetConsistencySingleEvent()
    {
        $eventId = 1;
        $endpoint = '/api/events/'.$eventId;
        $resource = new EventResource(Event::findOrFail($eventId));
        $events = json_encode($resource->resolve($resource));
        $token = str_replace("base64:/", "", env("APP_KEY"));
        $response = $this->call('GET', $endpoint, ['token' => $token]);
        $respEvent = $response->getContent();
        $this->assertEquals($events, $respEvent);
    }
}
