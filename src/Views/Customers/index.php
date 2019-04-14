<?php
/**
 * @var $this->form \App\Lib\Form
 */
?>
<h1>Zoznam zákazníkov</h1>
<table class="table">
    <tr>
        <th>Meno</th>
        <th>Adresa 1</th>
        <th>Email</th>
    </tr>
<?php
foreach ($this->model as $customer) {
    ?>
    <tr>
        <td><?=$this->escape($customer->meno)?></td>
        <td><?=$customer->adresa1?></td>
        <td><?=$customer->email?></td>
    </tr>
    <?php
}
?>
</table>


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
        <div class="col-md-6">
            <div class="form-group">
                <?= $this->form->labelFor('email', '', ['class' => 'col-xs-3 control-label'], true) ?>
                <div class="col-xs-9">
                    <?= $this->form->inputField("email", ['class' => 'form-control', 'placeholder' => "novak@email.com", 'type' => 'text', 'autocomplete' => "email"]) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
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