<?php

use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Spatie\Visit\Exceptions\InvalidPayload;
use Spatie\Visit\Exceptions\NoUrlSpecified;
use Spatie\Visit\Exceptions\NoUserFound;
use Spatie\Visit\Tests\TestClasses\TestStat;

beforeEach(function () {
    Route::any('/', function () {
        return 'result from home route';
    });

    Route::get('exception', function () {
        throw new Exception("exception in route");
    });

    Route::get('my-contact-page', function () {
        return 'result from contact route';
    })->name('contact');

    Route::get('logged-in-user', function () {
        $userEmail = auth()->user()?->email;

        if (! $userEmail) {
            $userEmail = 'nobody';
        }

        return "{$userEmail} is logged in";
    });

    Route::post('payload', function (Request $request) {
        return $request->input('testKey');
    });

    Route::get('json', function () {
        return response()->json(['jsonKey' => 1]);
    });

    User::create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password',
    ]);
});

it('will make GET requests by default', function () {
    Artisan::call('visit /');

    expectOutputContains(
        'GET /',
        200,
        'result from home route',
    );
});

it('can handle all methods', function (string $method) {
    Artisan::call("visit / --method={$method}");

    expectOutputContains(
        strtoupper($method) . ' /',
        200,
        'result from home route',
    );
})->with(['get', 'post', 'patch', 'put', 'delete']);

it('can handle a missing route', function () {
    Artisan::call("visit /non-existing-route");

    expectOutputContains('GET /non-existing-route', 404);
});

it('can handle a route with an exception', function () {
    Artisan::call("visit /exception");

    expectOutputContains('GET /exception', 500);
});

it('can display the underlying exception', function () {
    Artisan::call("visit /exception --show-exception");
})->throws('exception in route');

it('can visit a route using a route name', function () {
    Artisan::call("visit --route=contact");

    expectOutputContains(
        'GET /my-contact-page',
        200,
        'result from contact route',
    );
});

it('will throw a dedicated exception when not specifying a url or route', function () {
    Artisan::call("visit");
})->throws(NoUrlSpecified::class);

it('will not log in a user by default', function () {
    Artisan::call("visit /logged-in-user");

    expectOutputContains('GET /logged-in-user', 'nobody is logged in');
});

it('can log in user by id', function () {
    Artisan::call("visit /logged-in-user --user=1");

    expectOutputContains('GET /logged-in-user', 'john@example.com is logged in');
});

it('will not log in a non-existing user by id', function () {
    Artisan::call("visit /logged-in-user --user=2");
})->throws(NoUserFound::class);

it('can log in user by email', function () {
    Artisan::call("visit /logged-in-user --user=john@example.com");

    expectOutputContains('GET /logged-in-user', 'john@example.com is logged in');
});

it('will not log in a non-existing user by email', function () {
    Artisan::call("visit /logged-in-user --user=non-existing@example.com");
})->throws(NoUserFound::class);

it('will accept json as payload', function () {
    $jsonPayload = json_encode(['testKey' => 'testValue']);

    $jsonPayload = escapeshellarg($jsonPayload);

    Artisan::call("visit /payload --method=post --payload={$jsonPayload}");

    expectOutputContains('POST /payload', 'testValue');
})->skip(runningOnWindows(), 'This feature is not supported on Windows');

it('will not accept invalid json as payload', function () {
    Artisan::call("visit /payload --method=post --payload=blabla");
})->throws(InvalidPayload::class);


it('can output json', function () {
    Artisan::call("visit /json");

    expectOutputContains('GET /json', 'jsonKey');
});

it('can output html as text', function () {
    Route::get('html', function () {
        return '<html><a href="https://spatie.be">Homepage</a></html>';
    });

    Artisan::call("visit /html --text");

    expectOutputContains('GET /html', '[Homepage](https://spatie.be)');
});

it('can display custom stats', function () {
    config()->set('visit.stats', [TestStat::class]);

    Artisan::call("visit /");

    expectOutputContains('GET /', 'Test Stat Name', 'test stat value');
});

it('can filter json content', function () {
    Route::get('/filter-json', function () {
        return response()->json([
            'firstName' => 'firstValue',
            'nested' => [
                'secondName' => 'secondValue',
            ],
        ]);
    });

    Artisan::call("visit /filter-json --filter=nested.secondName");

    expectOutputContains('GET /filter-json', 'secondValue');
    expectOutputDoesNotContain('firstValue');
});

it('can filter html content', function () {
    Route::get('filter-html', function () {
        return '<html><body><div>First div</div><p>First paragraph</p><p>Second paragraph</p></body></html>';
    });

    Artisan::call('visit /filter-html --filter="p"');

    expectOutputContains('GET /filter-html', '<p>First paragraph</p><p>Second paragraph</p>');
    expectOutputDoesNotContain('First div');
});

it('will not show redirect if there are none', function () {
    Artisan::call('visit / --follow-redirects');

    expectOutputDoesNotContain('Redirects');
});

it('will show all redirects', function () {
    Route::get('redirect-from', function () {
        return redirect('redirect-to');
    });

    Route::get('redirect-to', function () {
        return 'You have arrived';
    });

    Artisan::call('visit /redirect-from --follow-redirects');

    expectOutputContains('GET /redirect-to', 'Redirects', '302 /redirect-from');
});
