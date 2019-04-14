<?php
/**
 * @var $this->form \App\Forms\Form
 */
?>
<h1>Zoznam tovaru</h1>


<div class="card-columns pt-5">
    <?php
    foreach ($this->model as $item) {
        ?>
        <div class="card">
            <?php if($item->obrazok): ?>
            <img class="card-img-top" src="<?=$item->obrazok?>" alt="Card image cap">
            <?php endif; ?>
            <div class="card-body">
                <h5 class="card-title"><?=$this->escape($item->nazov)?></h5>
                <p class="card-text">
                <?php
                if ($item->zasoby >= 1) echo "<span class='text-success'>Na sklade: {$item->zasoby}</span>";
                else echo "<span class='text-danger'>Nie je na sklade</span>";
                ?>
                </p>
                <p class="card-text">Cena: <?=$item->cena?> €</p>
                <?php if($item->zasoby >= 1): ?>
                <a href="/kosik?add=<?=$item->id?>" class="card-link">Pridať do košíka</a>
                <?php endif; ?>
            </div>
        </div>

        <?php
    }
    ?>


</div>
