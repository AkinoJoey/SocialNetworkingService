<?php

use src\response\render\HTMLRenderer;

return [
    'signup' =>function(): HTMLRenderer{
        return new HTMLRenderer('page/signup');
    }
];