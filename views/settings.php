<?php defined('APPLICATION') or die; ?>

<h1><?= $this->title() ?></h1>
<div class="Info"><?= t('If discussion or comment count is not relevant, please set them to zero. If both are zero, rule is not active (what rule should that be anyway)') ?></div>
<?= $this->Form->open(), $this->Form->errors() ?>
<div class="FormWrapper FormWrapper-Condensed">
<ul>
    <li>
        <?= $this->Form->label('Minimum Comments', 'promoteOnPostCount.MinComments') ?>
        <?= $this->Form->textBox('promoteOnPostCount.MinComments', ['type' => 'number', 'class' => 'SmallInput']) ?>
    </li>
    <li>
        <?= $this->Form->label('Connecting Condition', 'promoteOnPostCount.Connector') ?>
        <?= $this->Form->dropDown('promoteOnPostCount.Connector', ['AND' => 'AND', 'OR' => 'OR']) ?>
    </li>
    <li>
        <?= $this->Form->label('Minimum Discussions', 'promoteOnPostCount.MinDiscussions') ?>
        <?= $this->Form->textBox('promoteOnPostCount.MinDiscussions', ['type' => 'number', 'class' => 'SmallInput']) ?>
    </li>
    <li>
        <?= $this->Form->label('From Role', 'promoteOnPostCount.FromRoleID') ?>
        <?= $this->Form->dropDown('promoteOnPostCount.FromRoleID', $this->data('AvailableRoles')) ?>
    </li>
    <li>
        <?= $this->Form->label('To Role', 'promoteOnPostCount.ToRoleID') ?>
        <?= $this->Form->dropDown('promoteOnPostCount.ToRoleID', $this->data('AvailableRoles')) ?>
    </li>
</ul>
</div>

<div class="Buttons">
<?= $this->Form->button('Save') ?>
</div>
<?= $this->Form->close() ?>
