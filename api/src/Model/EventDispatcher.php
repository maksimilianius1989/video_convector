<?php


namespace Api\Model;


interface EventDispatcher
{
    public function dispatch(...$events): void;
}