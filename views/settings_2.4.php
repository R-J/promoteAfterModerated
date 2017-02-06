<?php defined('APPLICATION') or die; ?>
<h1><?= $this->title() ?></h1>

<?= helpAsset(t('Hint'), t('Promotion is done when a post gets approved. So this plugin is only useful for roles which require approval.')) ?>

<?= $this->Form->open(), $this->Form->errors() ?>

<ul>
    <li class="form-group"><h2 class="padded-left"><?= t('Roles') ?></h2></li>
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

    <li class="form-group"><h2 class="padded-left"><?= t('Either Comment and Discussion...') ?></h2></li>
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

    <li class="form-group"><h2 class="padded-left"><?= t('... or Posts in general') ?></h2></li>
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

<div class="Buttons">
<?= $this->Form->button('Save') ?>
</div>
<?= $this->Form->close() ?>
