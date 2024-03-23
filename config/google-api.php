<?php

return [
    'web' => [
        'client_id' => '949502318807-fp7dhmp0nn5crjif471kap7ij041c3kj.apps.googleusercontent.com',
        'project_id' => 'personalcalendar-418108',
        'auth_uri' => 'https://accounts.google.com/o/oauth2/auth',
        'token_uri' => 'https://oauth2.googleapis.com/token',
        'auth_provider_x509_cert_url' => 'https://www.googleapis.com/oauth2/v1/certs',
        'client_secret' => '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',
        'redirect_uris' => [
            "http://localhost/google-calendar/get-code",
            "http://localhost/admin/google-calendar/get-code"
        ]
    ]
];
