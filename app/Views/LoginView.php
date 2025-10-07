<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TUSBinRight</title>
    <style>
        label {
            color: red;
        }
    </style>
</head>
<body>
    <h1>Login - TUSBinRight</h1>

    <?php if(isset($login_error)) { echo $login_error; } ?>

    <?php echo form_open('login') ?>
    <h5>Email</h5>
    <input type="text" name="email" value="<?= set_value('email'); ?>" size="50" />
    <label for="email">
        <?php if(isset($validation)) { echo $validation->getError('email'); } ?>
    </label>

    <h5>Password</h5>
    <input type="password" name="password" value="<?= set_value('password'); ?>" size="50" />
    <label for="password">
        <?php if(isset($validation)) { echo $validation->getError('password'); } ?>
    </label>

    <div style="margin-top: 10px;">
        <input type="submit" value="Login" />
    </div>

    </form>
</body>
</html>
