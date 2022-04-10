<?php

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'jwt' => [
        'issuer' => $_ENV['JWT_ISSUER'],
        'audience' => $_ENV['JWT_AUDIENCE'],
        'id' => $_ENV['JWT_ID'],
        'expire' => $_ENV['JWT_EXPIRE'],
    ]
];
