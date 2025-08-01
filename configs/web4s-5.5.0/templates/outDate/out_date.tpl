<!DOCTYPE html>
<html>
<head>
    <title>Website hết hạn sử dụng</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <style type="text/css">
        body {
            font-family: "Inter", sans-serif;
            background-color: #EDF7FF;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: #333;
        }

        .container {
            text-align: center;
            animation: fadeIn 1s ease-in-out;
        }

        .container img {
            border-radius: 10px;
            object-fit: cover;
            height: 414px;
            width: 100%;
            max-width: 100%;
            margin-bottom: 0px;
        }

        .main-image {
            background-image: url(/admin/assets/media/error/notification_out_date.png);
            background-position: center;
            width: 100%;
            height: 460px;
            margin-top: 3rem;
            margin-bottom: 3rem;
        }
        .title-main {
            font-size: 31px;
            margin-bottom: 10px;
            color: #0D5DD6;
        }

        .subheading {
            color: #262942;
            margin-bottom: 15px;
            line-height: 1.5;
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 0.26px;
        }
        .contact span {
            font-weight: bold;
            color: #ff6601;
        }
        .text {
            font-size: 16px;
            color: #777;
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            background-color: #28a745;
            color: #ffffff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #218838;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        span.decor_content {
            margin-right: 0;
            margin-left: 0;
            right: 0;
            left: 0;
            top: -105px;
            position: absolute;
            color: #EDF7FF;
            text-shadow: 1px 2px 5px #acc9e094;
            font-size: 100px;
        }
        .content {
            position: relative;
        }
        .content_bottom {
            position: relative;
            z-index: 9;
        }
        span.btn.notification {
            line-height: 1.7;
            height: 1.7rem;
            width: 7rem;
            font-size: 16px;
            color: #0D5DD6;
            background: #EDF7FF;
            border: 1px solid #0D5DD6;
        }
    </style>
</head>
<body>
    <div class="container card">
        <div class="content">
            <span class="decor_content">OOP!</span>
            <div class="content_bottom">
                <h1 class="title-main">
                Website đã hết hạn sử dụng!
                </h1>
            </div>
        </div>
        <div class="main-image">
        </div>
        <p class="subheading">
                    Liên hệ với chúng tôi gia hạn: Website: www.web4s.vn   |   Hotline: 1900.6680 - 0901191616 | Email: contact@sm4s.vn 
                </p>
    </div>
</body>
</html>

