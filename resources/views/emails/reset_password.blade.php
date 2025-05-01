<!DOCTYPE html>
<html>

<head>
    <title>Reset Your Password</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; margin: 0; padding: 0;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);">
        
        <div style="text-align: center; margin-bottom: 20px;">
            {{-- <img src="https://ibb.co/5VTrfFy" alt="Logo" style="max-width: 150px;"> --}}
            <img src="https://i.ibb.co/jFhy1R2/Logo-With-Black.png" alt="Logo" border="0">
        </div>

        <h1 style="font-size: 24px; color: #333333; text-align: center; font-weight: bold;">Hello,</h1>

        <p style="font-size: 18px; color: #333333; text-align: center; margin-top: 10px;">
            We received a request to reset your password. Click the button below to reset it:
        </p>

        <div style="text-align: center; margin: 20px 0;">
            <a href="{{ $resetUrl }}" style="display: inline-block; background-color: #40C1BD; color: white; text-decoration: none; padding: 15px 30px; font-size: 18px; border-radius: 25px; font-weight: bold;">
                Reset Password
            </a>
        </div>

        <p style="font-size: 16px; color: #333333; text-align: center;">
            If you didn’t request a password reset, please ignore this email.
        </p>

        <p style="font-size: 16px; color: #333333; text-align: center; margin-top: 10px;">Thanks</p>

        <p style="font-size: 16px; color: #40C1BD; text-align: center; font-family: monospace; font-weight: bold;">
            Balance Team
        </p>

    </div>
    <script>

    </script>
</body>


</html>
