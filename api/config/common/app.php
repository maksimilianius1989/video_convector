<?php

declare(strict_types=1);

use Api\Http\Action\HomeAction;

return [
    HomeAction::class => function  () {
        return new HomeAction();
    }
];