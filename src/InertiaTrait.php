<?php


namespace Takashato\Inertia;


trait InertiaTrait
{
    public function getInertia(): Factory
    {
        return new Factory();
    }
}
