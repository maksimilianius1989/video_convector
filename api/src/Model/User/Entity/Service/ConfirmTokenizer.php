<?php

declare(strint_type=1);

namespace Api\Model\User\Service;

use Api\Model\User\Entity\User\ConfirmToken;

interface ConfirmTokenizer
{
    public function generate(): ConfirmToken;
}