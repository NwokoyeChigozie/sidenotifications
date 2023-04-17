<?php

abstract class TestCase extends Laravel\Lumen\Testing\TestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    protected function headers($token)
    {
        $headers = ['Accept' => 'application/json'];

        $headers['Authorization'] = 'Bearer '.$token;

        return $headers;
    }
}
