<?php

namespace src\response;

interface HTTPRenderer
{
    public function getFields(): array;
    public function getContent(): string;
}
