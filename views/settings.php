<?php defined('APPLICATION') or die; ?>
<style>
fieldset{
    border:1px solid #CCCCCC;
    width:580px;
    padding:5px;
    margin-bottom:6px;
}
</style>
<h1><?= $this->title() ?></h1>
<div class="Info"><?= t('If discussion, comment or post count is not relevant, please set them to zero.<br />Promotion is done when a post gets approved. So this plugin is only useful for roles which require approval.') ?></div>
<div class="Warning"><?= t('<strong>Either</strong> comment/discussion count should be set <strong>or</strong> post count.') ?></div>
<?= $this->Form->open(), $this->Form->errors() ?>
<div class="FormWrapper FormWrapper-Condensed">
<ul>
    <li>
        <fieldset>
            <legend><?= t('Roles') ?></legend>
            <?= $this->Form->label('The role a user will be promoted to', 'promoteAfterModerated.ToRoleID') ?>
            <?= $this->Form->dropDown('promoteAfterModerated.ToRoleID', $this->data('AvailableRoles')) ?>
            <?= $this->Form->label('The role that should be revoked', 'promoteAfterModerated.FromRoleID') ?>
            <?= $this->Form->dropDown('promoteAfterModerated.FromRoleID', $this->data('AvailableRoles')) ?>
        </fieldset>
    </li>
    <li>
        <fieldset>
            <legend><?= t('Either Comment and Discussion') ?></legend>
            <?= $this->Form->label('Minimum Comments', 'promoteAfterModerated.MinComments') ?>
            <?= $this->Form->textBox('promoteAfterModerated.MinComments', ['type' => 'number', 'class' => 'SmallInput']) ?>
            <?= $this->Form->label('Minimum Discussions', 'promoteAfterModerated.MinDiscussions') ?>
            <?= $this->Form->textBox('promoteAfterModerated.MinDiscussions', ['type' => 'number', 'class' => 'SmallInput']) ?>
        </fieldset>
    </li>
    <li>
        <fieldset>
            <legend><?= t('Or Posts in general') ?></legend>
            <?= $this->Form->label('Minimum Posts', 'promoteAfterModerated.MinPosts') ?>
            <?= $this->Form->textBox('promoteAfterModerated.MinPosts', ['type' => 'number', 'class' => 'SmallInput']) ?>
        </fieldset>
    </li>
</ul>
</div>
<div class="Buttons">
<?= $this->Form->button('Save') ?>
</div>
<?= $this->Form->close() ?>
