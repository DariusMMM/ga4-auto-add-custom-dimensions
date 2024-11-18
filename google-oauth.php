<?php

// Create a JSON Web Token (JWT) header
function createJwtHeader() {
    $jwtHeader = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
    
    return $jwtHeader;
}

// Create a JWT payload
function createJwtPayload($clientEmail, $tokenUrl) {
    $jwtPayload = base64_encode(json_encode([
        'iss' => $clientEmail,
        'scope' => 'https://www.googleapis.com/auth/analytics.edit',
        'aud' => $tokenUrl,
        'exp' => time() + 3600, // 1 hour expiration
        'iat' => time()
    ]));
    
    return $jwtPayload;
}

// Create the JWT signature
function createJwt($jwtHeader, $jwtPayload, $privateKey) {
    $unsignedToken = "$jwtHeader.$jwtPayload";
    $signature = '';
    openssl_sign($unsignedToken, $signature, $privateKey, 'sha256');
    $jwtSignature = base64_encode($signature);
    
    // Combine the parts to form the final JWT
    $jwt = "$unsignedToken.$jwtSignature";
    
    return $jwt;
}
