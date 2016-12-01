<?php

$PluginInfo['promoteOnPostCount'] = [
    'Name' => 'Promote on Post Count',
    'Description' => 'Allows rank promotionn based on post count',
    'Version' => '0.1',
    'RequiredApplications' => ['Vanilla' => '2.3'],
    'MobileFriendly' => true,
    'HasLocale' => true,
    'Author' => 'Robin Jurinka',
    'AuthorUrl' => 'https://vanillaforums.org/profile/R_J',
    'SettingsUrl' => '/plugin/promoteonpostcount',
    'License' => 'MIT'
];
/**
 * @package promoteOnPostCount
 * @author Robin Jurinka
 * @license MIT
 */
class PromoteOnPostCountPlugin extends Gdn_Plugin {
    public function base_getAppSettingsMenuItems_handler($sender, $args) {
        $menu = &$sender->EventArguments['SideMenu'];
        $menu->addLink(
            'Users',
            t('Promotions'),
            'plugin/promoteonpostcount',
            'Garden.Settings.Manage'
        );
    }

    public function pluginController_promoteOnPostCount_create($sender) {
        $sender->permission('Garden.Settings.Manage');
        $sender->addSideMenu('plugin/promoteonpostcount');

        $sender->Form = new Gdn_Form();

        $this->dispatch($sender, $sender->RequestArgs);
    }

    public function controller_index($sender, $args) {
$example1 = [
    4 => [
        'MinComments' => 3,
        'MinDiscussions' => 1,
        'Connector' => 'OR', // 'AND'
    ],
    8 => [
        'MinComments' => 5,
        'MinDiscussions' => -1,
        'Connector' => 'AND', // 'AND'
    ]
];
saveToConfig('promoteOnPostCount.Rules', serialize($example1));

        $sender->permission('Garden.Settings.Manage');
        // Get current promotions.
        $promotions = c('promoteOnPostCount.Rules', []);
        if (!is_array($promotions)) {
            $promotions = unserialize($promotions);
        }
        $sender->setData('Promotions', $promotions);

        // Get role names.
        $roleModel = new RoleModel();
        $sender->setData('Roles', $roleModel->getArray());

        $sender->setData('Title', t('Edit Promotion'));

        $sender->render($this->getView('index.php'));
    }

    public function controller_edit($sender, $args) {
decho($args);
        $sender->permission('Garden.Settings.Manage');

        // Get role names.
        $roleModel = new RoleModel();
        $roles = $roleModel->getArray();
        // Filter out admins and mods (too dangerous).
        $adminRoles = $roleModel->getbyType($roleModel::TYPE_ADMINISTRATOR);
        foreach ($adminRoles as $role) {
            unset($roles[$role->RoleID]);
        }
        $modRoles = $roleModel->getbyType($roleModel::TYPE_MODERATOR);
        foreach ($modRoles as $role) {
            unset($roles[$role->RoleID]);
        }
        $sender->setData('Roles', $roles);

        $sender->setData('Title', t('Edit Promotion'));
        $sender->render($this->getView('edit.php'));
    }

    public function controller_delete($sender, $args) {
        $sender->permission('Garden.Settings.Manage');
        $sender->setData('Title', t('Delete Promotion'));
        $sender->setData('RoleName', rawurldecode($args[1]));

        $sender->render($this->getView('delete.php'));
    }
}
