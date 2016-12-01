<?php defined('APPLICATION') or die;

?>
<h1><?= $this->title() ?></h1>
<div class="Info"><?= t('PromoteOnPostCount.Info', 'Every user who has the given number of comments/discussions below, will become member of the specified role. In order to prevent fatal errors, this will not work with the default moderator and admin role.') ?></div>
<table class="AltRows">
    <thead>
        <tr>
            <th><?= t('Min. Comments') ?></th>
            <th><?= t('Connector') ?></th>
            <th><?= t('Min. Discussions') ?></th>
            <th><?= t('Role') ?></th>
            <th><?= t('Options') ?></th>

        </tr>
    </thead>
    <tbody>
        <?php foreach ($this->data('Promotions') as $roleID => $info): ?>
        <tr>
            <td><?= $info['MinComments'] ?></td>
            <td><?= $info['Connector'] ?></td>
            <td><?= $info['MinDiscussions'] ?></td>
            <td><?= $this->data('Roles')[$roleID] ?></td>
            <td>
                <a href="<?= url('plugin/promoteonpostcount/edit/'.$roleID) ?>" class="Popup SmallButton"><?= t('Edit') ?></a>
                <a href="<?= url('plugin/promoteonpostcount/delete/'.$roleID.'/'.rawurlencode($this->data('Roles')[$roleID])) ?>" class="Popup SmallButton Delete"><?= t('Delete') ?></a>
            </td>
        </tr><?php endforeach ?>
    </tbody>
</table>
<div class="P">
<a href="<?= url('plugin/promoteonpostcount/add') ?>"class="Button"><?= t('Add Promotion') ?></a>
</div>
