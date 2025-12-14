<?php

return [
    /*
    |--------------------------------------------------------------------------
    | JWT Authentication Secret
    |--------------------------------------------------------------------------
    |
    | Don't forget to set this in your .env file, it should be a string of 32
    | characters. If you generate a secret from the terminal, make sure to
    | wrap it in quotes.
    |
    */

    'secret' => env('JWT_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | JWT Time to Live
    |--------------------------------------------------------------------------
    |
    | Specify the length of time (in minutes) that the token will be valid for.
    | Defaults to 1 hour. You may also adjust this time to match your needs.
    |
    */

    'ttl' => env('JWT_TTL', 60 * 24), // 24 hours

    /*
    |--------------------------------------------------------------------------
    | JWT Refresh Time to Live
    |--------------------------------------------------------------------------
    |
    | Specify the length of time (in minutes) that the token can be refreshed
    | before it must be freshly authenticated. Defaults to 2 weeks.
    |
    */

    'refresh_ttl' => env('JWT_REFRESH_TTL', 60 * 24 * 14), // 2 weeks

    /*
    |--------------------------------------------------------------------------
    | JWT Hash Algorithm
    |--------------------------------------------------------------------------
    |
    | Specify the hashing algorithm that will be used to sign the token.
    |
    | See here: https://github.com/namshi/jose/tree/master/src/Namshi/JOSE/Signer/OpenSSL
    | for possible values.
    |
    */

    'algo' => env('JWT_ALGO', 'HS256'),

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | This is the User model that will be used to authenticate users.
    |
    */

    'user' => App\Models\User::class,

    /*
    |--------------------------------------------------------------------------
    | User Identifier
    |--------------------------------------------------------------------------
    |
    | Specify the unique identifier that will be used to identify users in the
    | JWT payload. This is typically the user's ID.
    |
    */

    'identifier' => 'id',

    /*
    |--------------------------------------------------------------------------
    | JWT Claims
    |--------------------------------------------------------------------------
    |
    | Specify the claims that will be included in the JWT payload. These claims
    | will be added to the token and can be accessed via the authenticated
    | user's token method.
    |
    */

    'claims' => [
        'id',
        'name',
        'email',
        'username',
        'role',
        'wilayah_id',
    ],

    /*
    |--------------------------------------------------------------------------
    | Blacklist Enabled
    |--------------------------------------------------------------------------
    |
    | If you want to blacklist tokens, you must set this to true. When a token
    | is blacklisted, it will be invalid and subsequent requests with it will
    | result in an UnauthorizedException.
    |
    */

    'blacklist_enabled' => env('JWT_BLACKLIST_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Blacklist Grace Period
    |--------------------------------------------------------------------------
    |
    | When multiple concurrent requests are made with the same JWT,
    | it is possible that some of them fail due to token regeneration
    | on every request.
    |
    | Set grace period in seconds to prevent parallel request failure.
    |
    */

    'blacklist_grace_period' => env('JWT_BLACKLIST_GRACE_PERIOD', 0),

    /*
    |--------------------------------------------------------------------------
    | Security Key
    |--------------------------------------------------------------------------
    |
    | Your security key, it is used to encrypt your tokens, but for simple needs,
    | we can store into the "secret" field. Use the JWT_ALGO together with the
    | secret to sign the token.
    |
    */

    'key' => env('JWT_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Queue
    |--------------------------------------------------------------------------
    |
    |
    */

    'queue' => env('JWT_QUEUE', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Cookie
    |--------------------------------------------------------------------------
    |
    |
    */

    'cookie' => env('JWT_COOKIE', 'token'),

    /*
    |--------------------------------------------------------------------------
    | Single Logout
    |--------------------------------------------------------------------------
    |
    |
    */

    'single_logout' => env('JWT_SINGLE_LOGOUT', true),

    /*
    |--------------------------------------------------------------------------
    | API Authentication Guard
    |--------------------------------------------------------------------------
    |
    | API authentication guard name, it will be used to authenticate API requests.
    |
    */

    'guard' => env('JWT_GUARD', 'api'),
];