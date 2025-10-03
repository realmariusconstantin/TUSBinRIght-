<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Form Validation Lab - Robert Connolly</title>
    <style>
        label {
            color: red;
        }
    </style>
</head>
<body>
    <h1>My Form Validation Lab - Robert Connolly</h1>
    <?php echo form_open('login') ?>
    <h5>Username</h5>
    <input type="text" name="username" value="<?= set_value('username'); ?>" size="50" />
        <label for="username"><?php if(isset($validation)) { echo $validation->getError('username'); }?></label>
    <h5>First name</h5>
    <input type="text" name="fname" value="<?= set_value('fname'); ?>" size="50" />
        <label for="fname"><?php if(isset($validation)) { echo $validation->getError('fname'); }?></label>
    <h5>Surname</h5>
    <input type="text" name="lname" value="<?= set_value('lname'); ?>" size="50" />
        <label for="lname"><?php if(isset($validation)) { echo $validation->getError('lname'); }?></label>
    <h5>Address 1</h5>
    <input type="text" name="addressline1" value="<?= set_value('addressline1'); ?>" size="50" />
        <label for="addressline1"><?php if(isset($validation)) { echo $validation->getError('addressline1'); }?></label>
    <h5>City</h5>
    <input type="text" name="city" value="<?= set_value('city'); ?>" size="50" />
        <label for="city"><?php if(isset($validation)) { echo $validation->getError('city'); }?></label>
    <h5>Email</h5>
    <input type="text" name="email" value="<?= set_value('email'); ?>" size="50" />
        <label for="email"><?php if(isset($validation)) { echo $validation->getError('email'); }?></label>
    <h5>Password</h5>
    <input type="text" name="password" value="<?= set_value('password'); ?>" size="50" />
        <label for="password"><?php if(isset($validation)) { echo $validation->getError('password'); }?></label>
    <h5>Type</h5>
    <input type="text" name="type" value="<?= set_value('type'); ?>" size="50" />
        <label for="type"><?php if(isset($validation)) { echo $validation->getError('type'); }?></label>
    <h5>Age</h5>
    <input type="text" name="age" value="<?= set_value('age'); ?>" size="50" />
        <label for="age"><?php if(isset($validation)) { echo $validation->getError('age'); }?></label>
    <div style="margin-top: 10px;"><input type="submit" value="Submit" /></div>
    </form>
</body>
</html>