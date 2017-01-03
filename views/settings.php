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
            <?= $this->Form->label('The role a user will be promoted to', 'promoteOnPostCount.ToRoleID') ?>
            <?= $this->Form->dropDown('promoteOnPostCount.ToRoleID', $this->data('AvailableRoles')) ?>
            <?= $this->Form->label('The role that should be revoked', 'promoteOnPostCount.FromRoleID') ?>
            <?= $this->Form->dropDown('promoteOnPostCount.FromRoleID', $this->data('AvailableRoles')) ?>
        </fieldset>
    </li>
    <li>
        <fieldset>
            <legend><?= t('Either Comment and Discussion') ?></legend>
            <?= $this->Form->label('Minimum Comments', 'promoteOnPostCount.MinComments') ?>
            <?= $this->Form->textBox('promoteOnPostCount.MinComments', ['type' => 'number', 'class' => 'SmallInput']) ?>
            <?= $this->Form->label('Minimum Discussions', 'promoteOnPostCount.MinDiscussions') ?>
            <?= $this->Form->textBox('promoteOnPostCount.MinDiscussions', ['type' => 'number', 'class' => 'SmallInput']) ?>
        </fieldset>
    </li>
    <li>
        <fieldset>
            <legend><?= t('Or Posts in general') ?></legend>
            <?= $this->Form->label('Minimum Posts', 'promoteOnPostCount.MinPosts') ?>
            <?= $this->Form->textBox('promoteOnPostCount.MinPosts', ['type' => 'number', 'class' => 'SmallInput']) ?>
        </fieldset>
    </li>
</ul>
</div>
<div class="Buttons">
<?= $this->Form->button('Save') ?>
</div>
<?= $this->Form->close() ?>
