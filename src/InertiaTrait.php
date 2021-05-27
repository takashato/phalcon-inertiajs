<?php


namespace Takashato\Inertia;


trait InertiaTrait
{
    public function getInertia($shared = true): Factory
    {
        if ($shared) {
            $fromShared = $this->getDI()->getShared('inertia');
            if ($fromShared) {
                return $fromShared;
            }
            return new Factory();
        }
        return new Factory();
    }
}
