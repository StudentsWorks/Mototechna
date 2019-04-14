<?php
/**
 * @var $this->form \App\Lib\Form
 */
?>
<h1>Registrácia</h1>

<div class="jumbotron">
    <h1 class="display-4">Už ste zaregistrovaní?</h1>

    <hr class="my-4">
    <p>Prihláste sa, pre nakupovanie na tejto stránke.</p>
    <p class="lead">
        <a class="btn btn-primary btn-lg" href="/login" role="button">Prihlásiť sa</a>
    </p>
</div>
<?php if(!$this->form->successfull()): ?>
<form method="post" enctype="multipart/form-data" id="<?= $this->form->className() ?>" class="form-horizontal">
    <?php if ($this->form->hasErrors()): ?>
    <div class="alert alert-danger error-summary">
        <h4>Prosím, venujte pozornosť nasledujúcim chybám</h4>
        <?= $this->form->errorSummary() ?>
    </div>
<?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <?= $this->form->labelFor('name', '', ['class' => 'col-xs-3 control-label'], true) ?>
                <div class="col-xs-9">
                    <?= $this->form->inputField("name", ['class' => 'form-control', 'placeholder' => "Ján", 'type' => 'text', 'autocomplete' => "full-name"]) ?>
                </div>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <?= $this->form->labelFor('adresa1', '', ['class' => 'col-xs-3 control-label']) ?>
                <div class="col-xs-9">
                    <?= $this->form->inputField("adresa1", ['class' => 'form-control', 'placeholder' => "Pekná ulica 12", 'type' => 'text', 'autocomplete' => "street-address"]) ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <?= $this->form->labelFor('adresa2', '', ['class' => 'col-xs-3 control-label']) ?>
                <div class="col-xs-9">
                    <?= $this->form->inputField("adresa2", ['class' => 'form-control', 'placeholder' => "Kocúrkovo", 'type' => 'text', 'autocomplete' => "locality"]) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">

        <div class="col-md-6">
            <div class="form-group">
                <?= $this->form->labelFor('adresa3', '', ['class' => 'col-xs-3 control-label']) ?>
                <div class="col-xs-9">
                    <?= $this->form->inputField("adresa3", ['class' => 'form-control', 'placeholder' => "", 'type' => 'text']) ?>
                </div>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <?= $this->form->labelFor('email', '', ['class' => 'col-xs-3 control-label'], true) ?>
                <div class="col-xs-9">
                    <?= $this->form->inputField("email", ['class' => 'form-control', 'placeholder' => "novak@email.com", 'type' => 'text', 'autocomplete' => "email"]) ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <?= $this->form->labelFor('password', '', ['class' => 'col-xs-3 control-label'], true) ?>
                <div class="col-xs-9">
                    <?= $this->form->inputField("password", ['class' => 'form-control', 'placeholder' => "heslo", 'type' => 'password', 'autocomplete' => "password"]) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <button tabindex="0" class="btn btn-outline-primary" type="submit">Odoslať</button>
        </div>
    </div>

</form>
<?php endif; ?>