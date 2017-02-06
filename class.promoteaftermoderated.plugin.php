<?php

$PluginInfo['promoteAfterModerated'] = [
    'Name' => 'Promote After Moderated',
    'Description' => 'Allows automatic role changing after a given number of posts have been approved.',
    'Version' => '0.4',
    'RequiredApplications' => ['Vanilla' => '2.3'],
    'MobileFriendly' => true,
    'HasLocale' => true,
    'Author' => 'Robin Jurinka',
    'AuthorUrl' => 'https://vanillaforums.org/profile/r_j',
    'SettingsUrl' => '/settings/promoteaftermoderated',
    'License' => 'MIT'
];

/**
 * Plugin that allows automatic role promotion after X moderated posts.
 *
 * Admin can set a number for moderated comments and/or discussions after which
 * a user will be promoted. The role that should be revoked and the role that
 * will be given can be configured.
 */
class PromoteAfterModeratedPlugin extends Gdn_Plugin {
    /**
     * Pre-fill settings with sane settings.
     *
     * @return void.
     */
    public function setup() {
        touchConfig(
            'promoteAfterModerated.ToRoleID',
            RoleModel::getDefaultRoles(RoleModel::TYPE_MEMBER)
        );
        touchConfig('Preferences.Popup.RolePromotion', 1);
        $this->structure();
    }

    /**
     * Create activity type for role promotion.
     *
     * @return void.
     */
    public function structure() {
        $activityModel = new ActivityModel();
        $activityModel->defineType(
            'RolePromotion',
            [
                'Notify' => 1,
                'Public' => 0
            ]
        );
    }

    /**
     * Notify user of his role promotion.
     *
     * @param Integer $userID ID of the user to notify.
     * @param Integer $roleID ID of the role the user has been assigned.
     *
     * @return void.
     */
    private function roleChangeActivity($userID, $roleID) {
        $activityModel = new ActivityModel();
        $activityModel->queue(
            [
                'ActivityType' => 'RolePromotion',
                'ActivityUserID' => $userID,
                'RegardingUserID' => $userID,
                'NotifyUserID' => $userID,
                'HeadlineFormat' => t('{NotifyUserID,You} have been promoted.'),
                'Story' => t('Your posts do no longer require moderation.')
            ],
            'RolePromotion',
            ['Force' => true]
        );
        $activityModel->saveQueue();
    }

    /**
     * Settings page.
     *
     * @param SettingsController $sender Instance of the calling class.
     *
     * @return void.
     */
    public function settingsController_promoteAfterModerated_create($sender) {
        $sender->permission('Garden.Settings.Manage');

        $sender->addSideMenu('dashboard/settings/plugins');

        $sender->setData('Title', t('Promotion Rule'));

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
        $sender->setData('AvailableRoles', $roles);

        // Prepare form fields.
        $validation = new Gdn_Validation();
        $configurationModel = new Gdn_ConfigurationModel($validation);
        $configurationModel->setField(
            [
                'promoteAfterModerated.MinComments',
                'promoteAfterModerated.MinDiscussions',
                'promoteAfterModerated.MinPosts',
                'promoteAfterModerated.FromRoleID',
                'promoteAfterModerated.ToRoleID'
            ]
        );
        $sender->Form->setModel($configurationModel);

        if ($sender->Form->authenticatedPostBack() === false) {
            // If form is displayed "unposted".
            $sender->Form->setData($configurationModel->Data);
        } else {
            // Validate posted form.
            $sender->Form->validateRule('promoteAfterModerated.ToRoleID', 'ValidateRequired');
            $sender->Form->validateRule('promoteAfterModerated.ToRoleID', 'ValidateInteger');
            $sender->Form->validateRule('promoteAfterModerated.FromRoleID', 'ValidateRequired');
            $sender->Form->validateRule('promoteAfterModerated.FromRoleID', 'ValidateInteger');
            $sender->Form->validateRule('promoteAfterModerated.MinComments', 'ValidateRequired');
            $sender->Form->validateRule('promoteAfterModerated.MinComments', 'ValidateInteger');
            $sender->Form->validateRule('promoteAfterModerated.MinDiscussions', 'ValidateRequired');
            $sender->Form->validateRule('promoteAfterModerated.MinDiscussions', 'ValidateInteger');
            $sender->Form->validateRule('promoteAfterModerated.MinPosts', 'ValidateRequired');
            $sender->Form->validateRule('promoteAfterModerated.MinPosts', 'ValidateInteger');

            // Check if either comment/discussion or post is set, but not both.
            if ($sender->Form->getValue('promoteAfterModerated.MinPosts', 0) != 0) {
                if (
                    $sender->Form->getValue('promoteAfterModerated.MinComments', 0) +
                    $sender->Form->getValue('promoteAfterModerated.MinDiscussions', 0) > 0
                ) {
                    $sender->Form->addError('Please set either min. comment/discussion count or post count, but not both.');
                }
            }

            // Ensure that new role doesn't need moderation.
            $roleModel = new RoleModel();
            $permissions = $roleModel->getPermissions($sender->Form->getValue('promoteAfterModerated.ToRoleID'));
            if ($permissions[0]['Vanilla.Approval.Require'] == true) {
                $sender->Form->addError('This role hasn\'t permission to post unmoderated. Choosing this role doesn\'t make sense', 'promoteAfterModerated.ToRoleID');
            }
            // Try saving values.
            if ($sender->Form->save() !== false) {
                $sender->informMessage(
                    sprite('Check', 'InformSprite').t('Your settings have been saved.'),
                    ['CssClass' => 'Dismissable AutoDismiss HasSprite']
                );
            }
        }
        $sender->render($this->getView('settings_2.3.php'));
    }

    /**
     * Check if log entry is pending post and level up user.
     *
     * @param LogModel $sender Instance of the calling class.
     * @param mixed    $args   Event arguments.
     *
     * @return void.
     */
    public function logModel_afterRestore_handler($sender, $args) {
        // Only take action for pending posts.
        if (
            $args['Log']['Operation'] != 'Pending' ||
            !in_array($args['Log']['RecordType'], ['Comment', 'Discussion'])
        ) {
            return;
        }

        // Make sure plugin is configured.
        $config = c('promoteAfterModerated');
        $minComments = c('promoteAfterModerated.MinComments', false);
        $minDiscussions = c('promoteAfterModerated.MinDiscussions', false);
        $minPosts = c('promoteAfterModerated.MinPosts', false);
        $fromRoleID = c('promoteAfterModerated.FromRoleID', false);
        $roleID = c('promoteAfterModerated.ToRoleID', false);
        // All settings must be set.
        if (
            $minComments === false ||
            $minDiscussions === false ||
            $minPosts === false ||
            $roleID === false
        ) {
            return;
        }

        // Get the current users post counts.
        $countComments = Gdn::sql()->getCount(
            'Comment',
            ['InsertUserID' => $args['Log']['InsertUserID']]
        );
        $countDiscussions = Gdn::sql()->getCount(
            'Discussion',
            ['InsertUserID' => $args['Log']['InsertUserID']]
        );

        if ($minPosts > 0) {
            if ($countComments + $countDiscussions < $minPosts) {
                // Break if check for posts but not enough yet.
                return;
            }
        } elseif ($minComments > 0 || $minDiscussions > 0) {
            if ($countComments < $minComments || $countDiscussions < $minDiscussions) {
                // Break if comment and discussion check but not enough yet.
                return;
            }
        } else {
            // If not configured properly, break anyway.
            return;
        }

        // Get all current roles.
        $currentRoles = Gdn::userModel()->getRoles(
            $args['Log']['InsertUserID']
        )->resultArray();
        $newRoles = array_column($currentRoles, 'RoleID');

        // Ensure user has FromRoleID.
        if (!in_array($fromRoleID, $newRoles)) {
            return;
        }
        // Remove old role.
        $newRoles = array_diff($newRoles, [$fromRoleID]);

        // Add the new role.
        $newRoles[] = $roleID;

        // Level up!
        Gdn::userModel()->saveRoles(
            $args['Log']['InsertUserID'],
            $newRoles,
            true
        );
        $this->roleChangeActivity($args['Log']['InsertUserID'], $roleID);

        // Give feedback to admin.
        $user = Gdn::userModel()->getID($userID);
        Gdn::controller()->informMessage(sprintf(t('%1$s has been promoted and his/her posts will no longer need moderation'), $user->Name));
    }
}
