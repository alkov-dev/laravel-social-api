<?php

namespace App;

use OpenApi\Attributes as OA;

#[OA\OpenApi(
    openapi: "3.1.0",
    info: new OA\Info(
        version: "1.0.0",
        title: "My API",
        description: "Документация API проекта",
        contact: new OA\Contact(
            name: "Support",
            email: "support@example.com"
        )
    ),
    servers: [
        new OA\Server(
            url: "http://localhost:8000",
            description: "Local development server"
        ),
        new OA\Server(
            url: "https://api.example.com",
            description: "Production server"
        ),
    ],
    components: new OA\Components(
        securitySchemes: [
            new OA\SecurityScheme(
                securityScheme: "bearerAuth",
                type: "http",
                scheme: "bearer",
                bearerFormat: "token"
            )
        ]
    )
)]
class OpenApi {}


