<?php

namespace Tests\Traits;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\Response;

trait TestValidations
{
    protected function assertValidation(
        TestResponse $result,
        array $validationErrors,
        string $rule,
        array $ruleParams = []
    ) {
        $result->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors($validationErrors);

        foreach ($validationErrors as $field) {
            $result->assertJsonFragment([
                \Lang::get("validation.{$rule}", ['attribute' => str_replace('_', ' ', $field)] + $ruleParams), 
            ]);
        }
    }
}
