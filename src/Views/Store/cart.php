<?php
/**
 * @var $this->form \App\Forms\OrdersForm
 */
?>
<h1>Nákupný košík</h1>
<a class="btn btn-primary" href="/">Pokračovať v nákupe</a>
<br/>
<?php
if (!$this->form->successfull()):
?>
<table class="table">
    <tr>
        <th>Názov</th>
        <th>Množstvo</th>
        <th>Cena</th>
        <th></th>
    </tr>
<?php
if (count($this->cart) == 0):
?>
<tr><td colspan="4" class="text-center">Váš košík je prázdny. <br/><a class="btn btn-primary" href="/">Pokračovať v nákupe</a></td></tr>
    <?php
endif;
$suma = 0;
foreach ($this->model as $item) {
    $suma += $item->cena * $this->cart[$item->id];
    ?>
    <tr>
        <td><?=$this->escape($item->nazov)?></td>
        <td>
            <?=$this->cart[$item->id]?>
            &nbsp;

            <a disabled href="/kosik?remove=<?=$item->id?>"><big>-</big></a>
            /
            <a href="/kosik?add=<?=$item->id?>"><big>+</big></a>
        </td>
        <td><?=$item->cena?></td>
        <td><a href="/kosik?cancel=<?=$item->id?>">x</a></td>
    </tr>
    <?php
}
?>
    <tr>
        <th colspan="2">Cena spolu</th>
        <th colspan="2"><?=$suma?>€</th>
    </tr>
</table>

<h2>Objednávka</h2>
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
                <?= $this->form->labelFor('preprava', '', ['class' => 'col-xs-3 control-label'], true) ?>
                <div class="col-xs-9">
                    <?= $this->form->selectField("preprava", $this->preprava, ['class' => 'form-control', 'required' => 'required']) ?>
                </div>
            </div>
        </div>

    </div>


    <div class="row">
        <div class="col-12">
            <button name="<?=$this->form->className()?>[submit]" tabindex="0" class="btn btn-outline-primary" type="submit">Odoslať</button>
        </div>
    </div>

</form>

<?php else: ?>
Objednávka č. <?=$this->objednavka->id?> úspešne odoslaná
<?php endif; ?>
