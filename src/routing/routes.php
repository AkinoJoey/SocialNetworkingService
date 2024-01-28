<?php

use src\response\HTTPRenderer;
use src\response\render\HTMLRenderer;

return [
    '' => function (): HTTPRenderer {
        return new HTMLRenderer('page/top');
    },
    'guest' =>function () : HTTPRenderer{
        return new HTMLRenderer('page/guest');
    },
    'signup' =>function(): HTTPRenderer{
        return new HTMLRenderer('page/signup');
    },
    'login' =>function () : HTTPRenderer{
        return new HTMLRenderer('page/login');
    },
    'profile' => function (): HTTPRenderer {
        return new HTMLRenderer('page/profile');
    },
];