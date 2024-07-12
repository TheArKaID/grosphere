<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet"
        type='text/css' />
    <style>
        @media screen {
            @import url('https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap');
        }

        .message {
            position: relative;
            width: 100%;
            max-width: 36rem;
            margin: 0 auto;
            /* Center horizontally */
            background-color: #ffffff;
            box-sizing: border-box;
            /* Include padding in width calculation */
        }

        .button-container {
            text-align: center;
        }

        .upper-section {
            padding: 1.5rem 2.5rem;
            box-sizing: border-box;
            /* Include padding in height calculation */
        }

        .footer-section {
            height: 10rem;
            background-color: #266ffa;
            color: #ffffff;
            padding: 1.5rem 2.5rem;
            box-sizing: border-box;
            /* Include padding in height calculation */
        }

        .center {
            text-align: center;
            margin-bottom: 3.5rem;
        }

        .title-text {
            color: #454545;
            font-weight: 600;
            font-size: 1.5rem;
            line-height: 2rem;
            margin-top: 3.5rem;
        }

        .button {
            display: inline-block;
            text-decoration: none;
            height: 3rem;
            line-height: 3rem;
            padding: 0 1rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
            color: #ffffff;
            background-color: #266ffa;
            text-align: center;
            cursor: pointer;
        }

        .button:hover {
            background-color: #1e5abf;
        }

        p {
            color: #454545;
            margin-bottom: 1rem;
        }

        span {
            color: #454545;
            font-weight: 600;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body style='width: 100%; height: 100%; font-family: "Lato", sans-serif;'>
    <div class="message">
        <div class="upper-section">
            <div class="center">
                <img src='cid:logo-up.png' width="148" height="33" alt="Grosphere">
                <p class="title-text" style="color: #454545 font-weight: 600 font-size: 1.5rem margin-top: 3.5rem;">
                    @yield('title')
                </p>
            </div>
            <p style="margin-bottom: 2.5rem; color: #454545;">
                @yield('subtitle')
            </p>
            <p style="margin-bottom: 2.5rem; color: #454545;">
                @yield('content')
            </p>
            <p style="margin-bottom: 2.5rem; color: #454545;">
                @yield('send-by')
            </p>
            <div class="button-container" style="text-align: center;">
                <a class="button" href="{{ $subdomain }}"
                    style="display: inline-block; text-decoration: none; font-weight: 600;  margin: 0 0.5rem; height: 3rem; line-height: 3rem; padding: 0 1rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; color: #ffffff; background-color: #266ffa; text-align: center; cursor: pointer;">
                    Go to Grosphere</a>
            </div>
            <p style="margin-bottom: 4rem; color: #454545;">
                You can get in touch with us at
                <span style="color: #454545; font-weight: 600;">info@grosphere.sg</span>. We're
                here to help you at any step along the way.
            </p>
        </div>
        <div class="footer-section"
            style="width: 100%; height: 9rem; background-color: #266ffa; padding: 1.5rem 2.5rem;">
            <img width="27" height="24" src="cid:logo-down.png" alt="Grosphere">

            <div class="button-group" style="margin-top: 1rem">
                <a href="{{ $subdomain }}#home" style="text-decoration: none; color: #ffffff; font-weight: 600; margin: 0 0.5rem;">Home</a>
                <a href="{{ $subdomain }}#faq" style="text-decoration: none; color: #ffffff; font-weight: 600; margin: 0 0.5rem;">FAQ</a>
                <a href="{{ $subdomain }}/contact-us" style="text-decoration: none; color: #ffffff; font-weight: 600; margin: 0 0.5rem;">Contact Us</a>
            </div>
            <p style="color: #ffffff; margin-bottom: 1rem; font-size: 0.75rem; font-weight: 600;">
                2024 Copyright Grosphere. All rights reserved.
            </p>
        </div>
    </div>
</body>

</html>