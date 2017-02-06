<?php defined('APPLICATION') or die; ?>
<h1><?= $this->title() ?></h1>

<div class="alert alert-info padded"><?= t('Promotion is done when a post gets approved. So this plugin is only useful for roles which require approval.') ?></div>

<?= $this->Form->open(), $this->Form->errors() ?>

<fieldset>
    <legend><?= t('Roles') ?></legend>
    <ul>
        <li class="form-group">
            <div class="label-wrap">
                <?= $this->Form->label('The role a user will be promoted to', 'promoteAfterModerated.ToRoleID') ?>
                <div class="info">
                    <?= t('Choose a role which doesn\'t require moderation') ?>
                </div>
            </div>
            <div class="input-wrap">
                <?= $this->Form->dropDown('promoteAfterModerated.ToRoleID', $this->data('AvailableRoles')) ?>
            </div>
        </li>
        <li class="form-group">
            <div class="label-wrap">
                <?= $this->Form->label('The role that should be revoked', 'promoteAfterModerated.FromRoleID') ?>
            </div>
            <div class="input-wrap">
                <?= $this->Form->dropDown('promoteAfterModerated.FromRoleID', $this->data('AvailableRoles')) ?>
            </div>
        </li>
    </ul>
</fieldset>
<fieldset>
    <legend><?= t('Either Comment and Discussion...') ?></legend>
    <ul>
        <li class="form-group">
            <div class="label-wrap">
                <?= $this->Form->label('Minimum Comments', 'promoteAfterModerated.MinComments') ?>
                <div class="info">
                    <?= t('Please set to zero if irrelevant') ?>
                </div>
            </div>
            <div class="input-wrap">
                <?= $this->Form->textBox('promoteAfterModerated.MinComments', ['type' => 'number']) ?>
            </div>
        </li>
        <li class="form-group">
            <div class="label-wrap">
                <?= $this->Form->label('Minimum Discussions', 'promoteAfterModerated.MinDiscussions') ?>
                <div class="info">
                    <?= t('Please set to zero if irrelevant') ?>
                </div>
            </div>
            <div class="input-wrap">
                <?= $this->Form->textBox('promoteAfterModerated.MinDiscussions', ['type' => 'number']) ?>
            </div>
        </li>
    </ul>
</fieldset>

<fieldset>
    <legend><?= t('... or Posts in general') ?></legend>
    <ul>
        <li class="form-group">
            <div class="label-wrap">
                <?= $this->Form->label('Minimum Posts', 'promoteAfterModerated.MinPosts') ?>
                <div class="info">
                    <?= t('Please set to zero if irrelevant') ?>
                </div>
            </div>
            <div class="input-wrap">
                <?= $this->Form->textBox('promoteAfterModerated.MinPosts', ['type' => 'number']) ?>
            </div>
        </li>
    </ul>
</fieldset>

<div class="Buttons">
<?= $this->Form->button('Save') ?>
</div>

<?= $this->Form->close() ?>
