<?php defined('APPLICATION') or die; ?>

<h1><?= $this->data('Title') ?></h1>

<?= $this->Form->open(), $this->Form->errors() ?>

<div class="P"><?= t('Are you sure you want to delete this promotion rule?')) ?></div>
<div class="Buttons Buttons-Confirm">
<?= $this->Form->button('OK', ['class' => 'Button Primary']) ?>
<?= $this->Form->button('Cancel', ['type' => 'button', 'class' => 'Button Close']) ?>
<div>
<?= $this->Form->close() ?>
