<?php
    include_once('google-credentials.php');

    $params = array(
        'redirect_uri'  => $redirect_uri,
        'response_type' => 'code',
        'client_id'     => $client_id,
        'scope'         => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile'
    );

    echo $link = '<p><a href="' . $url . '?' . urldecode(http_build_query($params)) . '">Аутентификация через Google</a></p>';


