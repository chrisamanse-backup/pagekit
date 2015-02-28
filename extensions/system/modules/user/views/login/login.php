<form class="uk-form" action="<?= $app['url']->get('@system/auth/authenticate') ?>" method="post">

    <div class="uk-form-row">
        <input class="uk-width-1-1" type="text" name="credentials[username]" value="<?= $last_username ?>" placeholder="<?= __('username') ?>" autofocus>
    </div>
    <div class="uk-form-row">
        <input class="uk-width-1-1" type="password" name="credentials[password]" value="" placeholder="<?= __('password') ?>">
    </div>
    <div class="uk-form-row">
        <input class="uk-button uk-button-primary uk-width-1-1" type="submit" value="<?= __('Login') ?>">
    </div>

    <p>
        <label><input type="checkbox" name="<?= $remember_me_param ?>"> <?= __('Remember Me') ?></label>
        <br><a href="<?= $app['url']->get('@system/resetpassword') ?>"><?= __('Forgot Password?') ?></a>
        <?php if ($app['option']->get('system:user.registration', 'admin') != 'admin'): ?>
        <br><a href="<?= $app['url']->get('@system/registration') ?>"><?= __('Sign up') ?></a>
        <?php endif ?>
    </p>

    <input type="hidden" name="redirect" value="<?= $redirect ?>">
    <?php $this['token']->generate() ?>

</form>