<?php

namespace JoBins\Agents\Helpers;

class InputHelper
{
    public static function inputList(string|array $input): array
    {
        if (is_string($input)) {
            return [
                [
                    'content' => $input,
                    'role'    => 'user'
                ]
            ];
        }

        return $input;
    }
}
