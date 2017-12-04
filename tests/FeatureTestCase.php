<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;

class FeatureTestCase extends TestCase
{
    use RefreshDatabase;

    // Validación mensajes de error
    public function seeErrors(array $fields)
    {
        foreach ($fields as $name => $errors) {
            foreach ($errors as $message) {
                $this->seeInElement(
                    "field_{$name}.has-error .help.block", $message
                );
            }
        }
    }
}
