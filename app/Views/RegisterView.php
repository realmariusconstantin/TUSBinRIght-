<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - TUSBinRight</title>
    <style>
        label {
            color: red;
        }
    </style>
</head>
<body>
    <h1>Register - TUSBinRight</h1>

    <?php if(isset($register_error)) { echo $register_error; } ?>

    <?php echo form_open('register') ?>
    <h5>Forename</h5>
    <input type="text" name="forename" value="<?= set_value('forename'); ?>" size="50" />
    <label for="forename">
        <?php if(isset($validation)) { echo $validation->getError('forename'); } ?>
    </label>

    <h5>Surname</h5>
    <input type="text" name="surname" value="<?= set_value('surname'); ?>" size="50" />
    <label for="surname">
        <?php if(isset($validation)) { echo $validation->getError('surname'); } ?>
    </label>

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

    <h5>Confirm Password</h5>
    <input type="password" name="pass_confirm" value="<?= set_value('pass_confirm'); ?>" size="50" />
    <label for="pass_confirm">
        <?php if(isset($validation)) { echo $validation->getError('pass_confirm'); } ?>
    </label>

    <div style="margin-top: 10px;">
        <input type="submit" value="Register" />
    </div>

    </form>
</body>
</html>