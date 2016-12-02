<?php defined('APPLICATION') or die; ?>

<h1><?= $this->title() ?></h1>

<?= $this->Form->open(), $this->Form->errors() ?>
<div class="FormWrapper FormWrapper-Condensed">
<ul>
    <li>
        <?= $this->Form->label('Minimum Comments', 'MinComments') ?>
        <?= $this->Form->textBox('MinComments', ['type' => 'number', 'class' => 'SmallInput']) ?>
    </li>
    <li>
        <?= $this->Form->label('Connecting Condition', 'Connector') ?>
        <?= $this->Form->dropDown('Connector', ['AND' => 'AND', 'OR' => 'OR']) ?>
    </li>
    <li>
        <?= $this->Form->label('Minimum Discussions', 'MinDiscussions') ?>
        <?= $this->Form->textBox('MinDiscussions', ['type' => 'number', 'class' => 'SmallInput']) ?>
    </li>
    <li>
        <?= $this->Form->label('Role', 'Role') ?>
        <?= $this->Form->dropDown('Role', $this->data('Roles')) ?>
    </li>
</ul>
</div>

<div class="Buttons">
<?= $this->Form->button('Save') ?>
</div>
<?= $this->Form->close() ?>
