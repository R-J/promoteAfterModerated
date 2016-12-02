<?php defined('APPLICATION') or die; ?>
<h1><?= $this->title() ?></h1>
<div class="Info"><?= t('PromoteOnPostCount.Info', 'Every user who has the given number of comments/discussions below, will become member of the specified role. In order to prevent fatal errors, this will not work with the default moderator and admin role.') ?></div>
<table class="AltRows">
    <thead>
        <tr>
            <th><?= t('Min. Comments') ?></th>
            <th><?= t('Connector') ?></th>
            <th><?= t('Min. Discussions') ?></th>
            <th><?= t('From Role') ?></th>
            <th><?= t('To Role') ?></th>
            <th><?= t('Options') ?></th>

        </tr>
    </thead>
    <tbody>
        <?php foreach ($this->data('Promotions') as $ruleID => $rule): ?>
        <tr>
            <td><?= $rule['MinComments'] ?></td>
            <td><?= $rule['Connector'] ?></td>
            <td><?= $rule['MinDiscussions'] ?></td>
            <td><?= $rule['FromRoleID'] ?></td>
            <td><?= $rule['ToRoleID'] ?></td>
            <td>
                <a href="<?= url('plugin/promoteonpostcount/edit/'.$ruleID) ?>" class="Popup SmallButton"><?= t('Edit') ?></a>
                <a href="<?= url('plugin/promoteonpostcount/delete/'.$ruleID) ?>" class="Popup SmallButton Delete"><?= t('Delete') ?></a>
            </td>
        </tr><?php endforeach ?>
    </tbody>
</table>
<div class="Buttons">
<a href="<?= url('plugin/promoteonpostcount/add') ?>"class="Popup Button"><?= t('Add Promotion') ?></a>
</div>
<script>
var myFunc = $.popup.close;
$.popup.close = function () {
  myFunc.apply(this, arguments); // preserve the arguments
  location.reload(); 
};
</script>