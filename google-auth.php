<?php
    include_once('google-credentials.php');
    require_once('utils.php');

    if (isset($_GET['code'])) {
        $result = false;

        $params = array(
            'client_id'     => $client_id,
            'client_secret' => $client_secret,
            'redirect_uri'  => $redirect_uri,
            'grant_type'    => 'authorization_code',
            'code'          => $_GET['code']
        );

        $url = 'https://accounts.google.com/o/oauth2/token';
    } else {
        die('Something goes wrong (1)...');
    }

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, urldecode(http_build_query($params)));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($curl);
    curl_close($curl);

    $tokenInfo = json_decode($result, true);

    /*
    {
        "access_token" : "ya29.AHES6ZTGg0TYv6x-FIGNB438AlH4sTu54C8_6jCJ-GY6b8AeD7NSOxQ",
        "token_type" : "Bearer",
        "expires_in" : 3599,
        "id_token" : "eyJhbGciOiJSUzI1NiIsImtpZCI6IjQwODg0NDZmZjY2YjVjNjY4ZmE5MGJjYTEzZGJhMGI5NjhmMzc3ZGYifQ.eyJpc3MiOiJhY2NvdW50cy5nb29nbGUuY29tIiwiaWQiOiIxMDExMDMzMDM1ODE1NjEyNzMwMTQiLCJzdWIiOiIxMDExMDMzMDM1ODE1NjEyNzMwMTQiLCJ0b2tlbl9oYXNoIjoiTlVJU0R6T2lCeGlWQ3hkODM5RGRJZyIsImF0X2hhc2giOiJOVUlTRHpPaUJ4aVZDeGQ4MzlEZElnIiwiZW1haWwiOiJzdGFuaXNsYXYucHJvdGFzZXZpY2hAZ21haWwuY29tIiwiYXVkIjoiMzMzOTM3MzE1MzE4LWZocGk0aTZjcDM2dnA0M2I3dHZpcGFoYTdxYjQ4ajNyLmFwcHMuZ29vZ2xldXNlcmNvbnRlbnQuY29tIiwiY2lkIjoiMzMzOTM3MzE1MzE4LWZocGk0aTZjcDM2dnA0M2I3dHZpcGFoYTdxYjQ4ajNyLmFwcHMuZ29vZ2xldXNlcmNvbnRlbnQuY29tIiwiYXpwIjoiMzMzOTM3MzE1MzE4LWZocGk0aTZjcDM2dnA0M2I3dHZpcGFoYTdxYjQ4ajNyLmFwcHMuZ29vZ2xldXNlcmNvbnRlbnQuY29tIiwidmVyaWZpZWRfZW1haWwiOiJ0cnVlIiwiZW1haWxfdmVyaWZpZWQiOiJ0cnVlIiwiaWF0IjoxMzYzNzcyOTM4LCJleHAiOjEzNjM3NzY4Mzh9.ZFkCkV5HIlQ-IefdCHtRLk0yCu5HRmaI90lmd57GMfxjLRiyjZ3pRUrbSngfwVww0d7RErvemKHJSsHQPk1A0IcVd64JpcR50WNcz7Qxq6SJyzsiHsAQtwvS-xms_Kw8kdoctl_7ZeE9tCD71vtczL429-pNihVY50goaZs5R14"
    }
    */

    $userInfo = null;

    if (isset($tokenInfo['access_token'])) {
        $params['access_token'] = $tokenInfo['access_token'];

        $uInfo = json_decode(file_get_contents('https://www.googleapis.com/oauth2/v1/userinfo' . '?' . urldecode(http_build_query($params))), true);

        if (isset($uInfo['id'])) {
            $userInfo = $uInfo;
            $result = true;
        } else {
            die('Something goes wrong (2)...');
        }
    } else {
        die('Something goes wrong (3)...');
    }

    if (!validateUserId($userInfo['id'])) {
        die('Something goes wrong (4)...');
    }

    $uid = $userInfo['id'];
    $login = $userInfo['name'];
    $pass = generateUserId();
    $mail = $userInfo['email'];

    if (!isUserIdExists($userInfo['id'])) {
        addUser($login, $pass, $mail, TRUE, $uid);

        if (!tryLogin($login, $pass))
        {
            die('Something goes wrong (5)...');
        }
    } else {
        $db = is_null($readydb) ? new PdoDb() : $readydb;

        $query =
            'UPDATE `users` SET `pass`=:pass, `salt`=:salt, `session`=:session WHERE `userid`=:userid';

        $salt = generateSalt();
        $saltedPass = saltPass($pass, $salt);
        $session = generateSession();

        $req = $db->prepare($query);
        $req->bindParam(':userid', $uid, PDO::PARAM_STR);
        $req->bindParam(':pass', $saltedPass, PDO::PARAM_STR);
        $req->bindParam(':salt', $salt, PDO::PARAM_STR);
        $req->bindParam(':session', $session, PDO::PARAM_STR);

        if (!$req->execute()) {
            die('Something goes wrong (6)...');
        }

        if (!tryLogin($login, $pass))
        {
            die('Something goes wrong (7)...');
        }
    }

    header('Location: https://russiancoders.dev/forum/');
