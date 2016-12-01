<?php defined('APPLICATION') or die; ?>

<h1><?= $this->title() ?></h1>

<?= $this->Form->open(), $this->Form->errors() ?>

<p>
        <?= $this->Form->label('Minimum Comments', 'MinComments') ?>
        <?= $this->Form->textBox('MinComments', ['type' => 'number', 'class' => 'SmallInput']) ?>
</p>

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

<?php /*
<table>
    <tr>
        <td><?= t('Minimum Comments') ?></td>
        <td><?= $this->Form->textBox('MinComments', ['type' => 'number']) ?></td>
    </tr>
    <tr>
        <td><?= t('Connecting Condition') ?></td>
        <td><?= $this->Form->dropDown('Connector', ['AND' => 'AND', 'OR' => 'OR']) ?></td>
    </tr>
    <tr>
        <td><?= t('Minimum Discussions') ?></td>
        <td><?= $this->Form->textBox('MinDiscussions', ['type' => 'number']) ?></td>
    </tr>
    <tr>
        <td><?= t('Minimum Discussions') ?></td>
        <td><?= $this->Form->dropDown('Role', $this->data('Roles')) ?></td>
    </tr>
</table>
*/ ?>

<div class="P">
<?= $this->Form->button('Save') ?>
</div>
<?= $this->Form->close() ?>
