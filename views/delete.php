<?php defined('APPLICATION') or die; ?>

<h1><?= $this->data('Title') ?></h1>

<?= $this->Form->open(), $this->Form->errors() ?>

<div class="P"><?= sprintf(t('Are you sure you want to delete the promotion for role "%s"?'), htmlspecialchars($this->data('RoleName'))) ?></div>
<div class="Buttons Buttons-Confirm">
<?= $this->Form->button('OK', ['class' => 'Button Primary']) ?>
<?= $this->Form->button('Cancel', ['type' => 'button', 'class' => 'Button Close']) ?>
<div>
<?= $this->Form->close() ?>
