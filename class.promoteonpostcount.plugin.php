<?php
$PluginInfo['promoteOnPostCount'] = [
    'Name' => 'Promote on Post Count',
    'Description' => 'Allows role promotion based on post count',
    'Version' => 'alpha',
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
    /**
     * Add menu item to dashboard.
     *
     * @param GardenController $sender Instance of the sendig object.
     * @param Mixed            $args   Event arguments
     *
     * @return void.
     */
    public function base_getAppSettingsMenuItems_handler($sender, $args) {
        $menu = &$args['SideMenu'];
        $menu->addLink(
            'Users',
            t('Promotions'),
            'plugin/promoteonpostcount',
            'Garden.Settings.Manage'
        );
    }

    /**
     * Dispatcher for plugins functionalities.
     *
     * @param PluginController $sender Instance of the calling object.
     *
     * @return void.
     */
    public function pluginController_promoteOnPostCount_create($sender) {
        $sender->permission('Garden.Settings.Manage');
        $sender->addSideMenu('plugin/promoteonpostcount');

        $sender->Form = new Gdn_Form();

        $this->dispatch($sender, $sender->RequestArgs);
    }

    /**
     * Show list of promotion rules.
     *
     * @param PluginController $sender Instance of the calling object.
     * @param Mixed            $args   Url parameters.
     *
     * @return void.
     */
    public function controller_index($sender, $args) {
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

    /**
     * Set title and re-use edit functionality.
     *
     * @param PluginController $sender Instance of the calling object.
     * @param Mixed            $args   Url parameters.
     *
     * @return void.
     */
    public function controller_add($sender, $args) {
        $sender->setData('Title', t('Add Promotion'));
        $this->controller_edit($sender, $args);
    }

    public function controller_edit($sender, $args) {
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
        if (count($args) > 0) {
            $sender->setData('Title', t('Edit Promotion'));
        }

        // What to do when form is shown for the first time in edit mode.
        if ($sender->Form->authenticatedPostBack() === false && count($args) > 0) {
            $promotions = c('promoteOnPostCount.Rules', []);
            if (!is_array($promotions)) {
                $promotions = unserialize($promotions);
            }
            $sender->Form->setValue(
                'MinComments',
                $promotions[$args[0]]['MinComments']
            );
            $sender->Form->setValue(
                'Connector',
                $promotions[$args[0]]['Connector']
            );
            $sender->Form->setValue(
                'MinDiscussions',
                $promotions[$args[0]]['MinDiscussions']
            );
            $sender->Form->setValue(
                'Role',
                $args[0]
            );
        }
        // Process form save.
        if ($sender->Form->authenticatedPostBack() == true) {
            $sender->Form->validateRule('MinComments', 'ValidateRequired');
            $sender->Form->validateRule('MinComments', 'ValidateInteger');
            $sender->Form->validateRule('Connector', 'ValidateRequired');
            $sender->Form->validateRule('MinDiscussions', 'ValidateRequired');
            $sender->Form->validateRule('MinDiscussions', 'ValidateInteger');
            $sender->Form->validateRule('Role', 'ValidateRequired');
            $sender->Form->validateRule('Role', 'ValidateInteger');

            if (!$sender->Form->validationResults()) {
                $formValues = $sender->Form->formValues();
                // Get current promotions.
                $promotions = c('promoteOnPostCount.Rules', []);
                if (!is_array($promotions)) {
                    $promotions = unserialize($promotions);
                }
                // Add form values.
                $promotions[$formValues['Role']] = [
                    'MinComments' => $formValues['MinComments'],
                    'Connector' => $formValues['Connector'],
                    'MinDiscussions' => $formValues['MinDiscussions']
                ];
                // Save to config.
                saveToConfig('promoteOnPostCount.Rules', serialize($promotions));
                // Give feedback.
                $sender->informMessage(
                    sprite('Check', 'InformSprite').t('Your settings have been saved.'),
                    ['CssClass' => 'Dismissable AutoDismiss HasSprite']
                );
                redirect($sender->SelfUrl);
            }

        }



        $sender->render($this->getView('addedit.php'));
    }

    public function controller_delete($sender, $args) {
        $sender->permission('Garden.Settings.Manage');
        $sender->setData('Title', t('Delete Promotion'));
        $sender->setData('RoleName', rawurldecode($args[1]));

        $sender->render($this->getView('delete.php'));
    }
}
