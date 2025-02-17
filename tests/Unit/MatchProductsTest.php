<?php

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};


uses(RefreshDatabase::class, WithFaker::class);

test('match products action works', function () {
    $action = app(\App\Actions\MatchProducts::class);
    $response = $action->run([
        "date_of_birth" => "1999-01-07",
        "monthly_gross_income" => 50000,
        "region" => "NCR",
        "limit" => 3,
        "verbose" => 0
    ]);

    $data= $response->toArray();
    $x = array_shift($data);
    expect(count($x))->toBeGreaterThan(0);
});
